<?php namespace App\Tests\JsonApi;

use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\TokenNotValidException;
use App\Tests\IlluminateTestCase;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthAndOauthTest extends IlluminateTestCase
{
    /** @var bool */
    public static $migrated = false;

    /** @var \stdClass */
    public static $oauthClientData;

    /** @var string */
    public static $passwordResetToken;

    public function setUp()
    {
        // Migrations should run only once, before application is created (the moment when $this->app == null).
        if (!static::$migrated) {
            $this->afterApplicationCreated(function () {
                $this->artisan('migrate:reset');
                $this->artisan('migrate');
            });
            static::$migrated = true;
        }

        parent::setUp();

        if (is_null(static::$oauthClientData)) {
            $this->artisan('passport:client', ['--password' => true, '--name' => 'PhpUnitTestClient']);
            static::$oauthClientData = app('db')->table('oauth_clients')->select('id', 'secret')->where('name', '=', 'PhpUnitTestClient')->first();
        }
    }

    /**
     * Tests 'GET /api/v1/register' endpoint for exceptions.
     */
    public function testRegisterWithGetMethodForException()
    {
        $response = $this->json('GET', '/api/v1/register');
        $response->assertStatus(405);
        $response->assertJson(['exception' => MethodNotAllowedHttpException::class]);
    }

    /**
     * Tests 'POST /api/v1/register' endpoint for exceptions.
     */
    public function testRegisterWithPostMethodForExceptions()
    {
        # Validation failure: Password confirmation missing
        $credentials = ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver'];
        $response = $this->json('POST', '/api/v1/register', $credentials);
        $response->assertStatus(422);
        $response->assertJson(['exception' => ValidationException::class]);
        $this->assertDatabaseMissing('users', ['email' => $credentials['email']]);

        # Validation failure: Email address missing
        $credentials = ['password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'];
        $response = $this->json('POST', '/api/v1/register', $credentials);
        $response->assertStatus(422);
        $response->assertJson(['exception' => ValidationException::class]);
    }

    /**
     * Tests 'POST /api/v1/register' endpoint for success.
     */
    public function testRegisterWithPostMethodForSuccess()
    {
        $credentials = ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'];
        $response = $this->json('POST', '/api/v1/register', $credentials);
        $response->assertStatus(201);
        $response->assertJson([
            'email' => 'john.doe@example.com'
        ]);
        $this->assertDatabaseHas('users', ['email' => $credentials['email']]);
    }

    /**
     * Tests 'POST /api/v1/password/email' endpoint for exceptions.
     *
     * @depends testRegisterWithPostMethodForSuccess
     */
    public function testSendPasswordResetLinkForExceptions()
    {
        # Validation failure: Invalid email address
        $userData = ['email' => 'jane.doe@'];
        $response = $this->json('POST', '/api/v1/password/email', $userData);
        $response->assertSee('message');
        $response->assertStatus(422);
        $response->assertJson(['exception' => ValidationException::class]);

        # User doesn't exist
        $userData = ['email' => 'jane.doe@example.com'];
        $response = $this->json('POST', '/api/v1/password/email', $userData);
        $response->assertSee('message');
        $response->assertStatus(404);
        $response->assertJson(['exception' => NotFoundHttpException::class]);
    }

    /**
     * Tests 'POST /api/v1/password/email' endpoint for success.
     *
     * @depends testRegisterWithPostMethodForSuccess
     */
    public function testSendPasswordResetLinkForSuccess()
    {
        $userData = ['email' => 'john.doe@example.com'];
        $response = $this->json('POST', '/api/v1/password/email', $userData);
        $response->assertStatus(200);
        $response->assertJson(['message' => trans('passwords.sent')]);
        static::$passwordResetToken = $response->decodeResponseJson()['token'];
    }

    /**
     * Tests 'POST /api/v1/password/reset' endpoint for exceptions.
     *
     * @depends testSendPasswordResetLinkForSuccess
     */
    public function testResetPasswordForExceptions()
    {
        # Validation failure: Token missing
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0'
        ];
        $response = $this->json('POST', '/api/v1/password/reset', $userData);
        $response->assertSee('message');
        $response->assertStatus(422);
        $response->assertJson(['exception' => ValidationException::class]);

        # Validation failure: Password confirmation mismatch
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rd',
            'token' => static::$passwordResetToken
        ];
        $response = $this->json('POST', '/api/v1/password/reset', $userData);
        $response->assertSee('message');
        $response->assertStatus(422);
        $response->assertJson(['exception' => ValidationException::class]);

        # Validation failure: Password confirmation missing
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'token' => static::$passwordResetToken
        ];
        $response = $this->json('POST', '/api/v1/password/reset', $userData);
        $response->assertSee('message');
        $response->assertStatus(422);
        $response->assertJson(['exception' => ValidationException::class]);

        # User doesn't exist
        $userData = [
            'id' => 2,
            'email' => 'jane.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
            'token' => static::$passwordResetToken
        ];
        $response = $this->json('POST', '/api/v1/password/reset', $userData);
        $response->assertSee('message');
        $response->assertStatus(404);
        $response->assertJson(['exception' => NotFoundHttpException::class]);

        # Invalid token
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
            'token' => 'wrong-token'
        ];
        $response = $this->json('POST', '/api/v1/password/reset', $userData);
        $response->assertSee('message');
        $response->assertStatus(422);
        $response->assertJson(['exception' => TokenNotValidException::class]);
    }

    /**
     * Tests 'POST /api/v1/password/reset' endpoint for success.
     *
     * @depends testSendPasswordResetLinkForSuccess
     */
    public function testPasswordResetPasswordForSuccess()
    {
        $userData = [
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
            'token' => static::$passwordResetToken
        ];
        $response = $this->json('POST', '/api/v1/password/reset', $userData);
        $response->assertStatus(200);
        $response->assertJson(['message' => trans('passwords.reset')]);
    }

    /**
     * * Tests requesting tokens for Password Grant Client, for exceptions.
     *
     * @depend testPasswordResetPasswordForSuccess
     */
    public function testOauthTokenForExceptions()
    {
        # Invalid client: wrong client id
        $user = [
            'grant_type' => 'password',
            'client_id' => 2, // Wrong client id
            'client_secret' => static::$oauthClientData->secret,
            'username' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'scope' => ''
        ];
        $response = $this->json('POST', '/oauth/token', $user);
        $response->assertStatus(401);
        $response->assertJson(['error' => 'invalid_client']);

        # Invalid request: missing username
        $user = [
            'grant_type' => 'password',
            'client_id' => static::$oauthClientData->id,
            'client_secret' => static::$oauthClientData->secret,
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'scope' => ''
        ];
        $response = $this->json('POST', '/oauth/token', $user);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'invalid_request']);

        # Invalid credentials
        $user = [
            'grant_type' => 'password',
            'client_id' => static::$oauthClientData->id,
            'client_secret' => static::$oauthClientData->secret,
            'username' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rd', // Wrong password
            'scope' => ''
        ];
        $response = $this->json('POST', '/oauth/token', $user);
        $response->assertStatus(401);
        $response->assertJson(['error' => 'invalid_credentials']);
    }

    /**
     * Tests requesting tokens for Password Grant Client, for success.
     *
     * @depends testPasswordResetPasswordForSuccess
     */
    public function testOauthTokenForSuccess()
    {
        $user = [
            'grant_type' => 'password',
            'client_id' => static::$oauthClientData->id,
            'client_secret' => static::$oauthClientData->secret,
            'username' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'scope' => ''
        ];
        $response = $this->json('POST', '/oauth/token', $user);
        $response->assertStatus(200);
        $response->assertJson(['token_type' => 'Bearer']);

        $this->assertNotEmpty($response->decodeResponseJson()['access_token']);
        $this->assertNotEmpty($response->decodeResponseJson()['refresh_token']);
    }
}

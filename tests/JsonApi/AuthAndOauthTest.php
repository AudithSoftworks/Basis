<?php namespace App\Tests\JsonApi;

use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\TokenNotValidException;
use App\Tests\IlluminateTestCase;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthAndOauthTest extends IlluminateTestCase
{
    /** @var array */
    public static $requestHeaders = ['HTTP_ACCEPT' => 'application/json'];

    /** @var \stdClass */
    public static $oauthClientData;

    /** @var string */
    public static $accessToken = '';

    /** @var string */
    public static $refreshToken = '';

    /**
     * Create an app instance for facades to work + Migrations.
     */
    public static function setUpBeforeClass()
    {
        putenv('APP_ENV=testing');
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        \Artisan::call('migrate:refresh');

        \Artisan::call('passport:client', ['--password' => true, '--name' => 'PhpUnitTestClient']);
        self::$oauthClientData = app('db')->table('oauth_clients')->select('id', 'secret')->where('name', '=', 'PhpUnitTestClient')->first();
    }

    /**
     * Tests 'GET /api/v1/register' endpoint for exceptions.
     */
    public function testRegisterWithGetMethodForException()
    {
        $this->get('/api/v1/register', self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(405);
        $this->seeJson(['exception' => MethodNotAllowedHttpException::class]);
    }

    /**
     * Tests 'POST /api/v1/register' endpoint for exceptions.
     */
    public function testRegisterWithPostMethodForExceptions()
    {
        # Validation failure: Password confirmation missing
        $credentials = ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver'];
        $this->post('/api/v1/register', $credentials, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);
        $this->notSeeInDatabase('users', ['email' => $credentials['email']]);

        # Validation failure: Email address missing
        $credentials = ['password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'];
        $this->post('/api/v1/register', $credentials, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);
    }

    /**
     * Tests 'POST /api/v1/register' endpoint for success.
     */
    public function testRegisterWithPostMethodForSuccess()
    {
        $credentials = ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'];
        $this->post('/api/v1/register', $credentials, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(201);
        $this->seeJson([
            'email' => 'john.doe@example.com'
        ]);
        $this->seeInDatabase('users', ['email' => $credentials['email']]);
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
        $this->post('/api/v1/password/email', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);

        # User doesn't exist
        $userData = ['email' => 'jane.doe@example.com'];
        $this->post('/api/v1/password/email', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(404);
        $this->seeJson(['exception' => NotFoundHttpException::class]);
    }

    /**
     * Tests 'POST /api/v1/password/email' endpoint for success.
     *
     * @depends testRegisterWithPostMethodForSuccess
     */
    public function testSendPasswordResetLinkForSuccess()
    {
        $userData = ['email' => 'john.doe@example.com'];
        $this->post('/api/v1/password/email', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['message' => trans('passwords.sent')]);
    }

    /**
     * Tests 'POST /api/v1/password/reset' endpoint for exceptions.
     *
     * @depends testSendPasswordResetLinkForSuccess
     */
    public function testResetPasswordForExceptions()
    {
        $passwordResetToken = app('db')->table('password_resets')->where('email', '=', 'john.doe@example.com')->value('token');

        # Validation failure: Token missing
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0'
        ];
        $this->post('/api/v1/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);

        # Validation failure: Password confirmation mismatch
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rd',
            'token' => $passwordResetToken
        ];
        $this->post('/api/v1/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);

        # Validation failure: Password confirmation missing
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'token' => $passwordResetToken
        ];
        $this->post('/api/v1/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);

        # User doesn't exist
        $userData = [
            'id' => 2,
            'email' => 'jane.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
            'token' => $passwordResetToken
        ];
        $this->post('/api/v1/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(404);
        $this->seeJson(['exception' => NotFoundHttpException::class]);

        # Invalid token
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
            'token' => 'wrong-token'
        ];
        $this->post('/api/v1/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => TokenNotValidException::class]);
    }

    /**
     * Tests 'POST /api/v1/password/reset' endpoint for success.
     *
     * @depends testSendPasswordResetLinkForSuccess
     */
    public function testPasswordResetPasswordForSuccess()
    {
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
            'token' => app('db')->table('password_resets')->where('email', '=', 'john.doe@example.com')->value('token')
        ];
        $this->post('/api/v1/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['message' => trans('passwords.reset')]);
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
            'client_secret' => self::$oauthClientData->secret,
            'username' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'scope' => ''
        ];
        $this->post('/oauth/token', $user, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(401);
        $this->seeJson(['error' => 'invalid_client']);

        # Invalid request: missing username
        $user = [
            'grant_type' => 'password',
            'client_id' => self::$oauthClientData->id,
            'client_secret' => self::$oauthClientData->secret,
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'scope' => ''
        ];
        $this->post('/oauth/token', $user, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(400);
        $this->seeJson(['error' => 'invalid_request']);

        # Invalid credentials
        $user = [
            'grant_type' => 'password',
            'client_id' => self::$oauthClientData->id,
            'client_secret' => self::$oauthClientData->secret,
            'username' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rd', // Wrong password
            'scope' => ''
        ];
        $this->post('/oauth/token', $user, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(401);
        $this->seeJson(['error' => 'invalid_credentials']);
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
            'client_id' => self::$oauthClientData->id,
            'client_secret' => self::$oauthClientData->secret,
            'username' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'scope' => ''
        ];
        $this->post('/oauth/token', $user, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['token_type' => 'Bearer']);

        $responseData = json_decode($this->response->getContent());

        self::$accessToken = $responseData->access_token;
        self::$refreshToken = $responseData->refresh_token;

        $this->assertNotEmpty(self::$accessToken);
        $this->assertNotEmpty(self::$refreshToken);
    }
}

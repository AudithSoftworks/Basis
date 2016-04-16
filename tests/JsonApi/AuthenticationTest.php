<?php namespace App\Tests\JsonApi;

use App\Exceptions\Common\NotImplementedException;
use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\LoginNotValidException;
use App\Exceptions\Users\PasswordNotValidException;
use App\Exceptions\Users\TokenNotValidException;
use App\Exceptions\Users\UserAlreadyActivatedException;
use App\Tests\IlluminateTestCase;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticationTest extends IlluminateTestCase
{
    /**
     * Since this is JSON API test suite, we need appropriate headers for requests.
     *
     * @var array
     */
    public static $requestHeaders = [
        'HTTP_ACCEPT' => 'application/json'
    ];

    /**
     * Create an app instance for facades to work + Migrations.
     */
    public static function setUpBeforeClass()
    {
        putenv('APP_ENV=testing');
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        \Artisan::call('migrate:refresh');
    }

    public function testCsrfMiddleWareBehaviorForNonApiHandling()
    {
        // Let's trick it to think that we are only sending AJAX request
        // (i.e. we are not appending 'Accept: application/json' to request headers).
        $temporaryRequestHeaders = ['HTTP_ACCEPT' => '', 'X-Requested-With' => 'XMLHttpRequest'];

        $this->post('/users', [], $temporaryRequestHeaders);
        $this->seeStatusCode(422);
    }

    /**
     * Tests App\Controllers\UsersController::create() resource method.
     *
     * @depends testCsrfMiddleWareBehaviorForNonApiHandling
     */
    public function testUsersCreate()
    {
        $this->get('/users/create', self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(405);
        $this->seeJson(['exception' => NotImplementedException::class]);
    }

    /**
     * Tests App\Controllers\UsersController::store() resource method.
     *
     * @depends testCsrfMiddleWareBehaviorForNonApiHandling
     */
    public function testUsersStoreForExceptions()
    {
        # Validation failure: Password confirmation missing
        $credentials = ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver'];
        $this->post('/users', $credentials, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);
        $this->notSeeInDatabase('users', ['email' => $credentials['email']]);

        # Validation failure: Email address missing
        $credentials = ['password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'];
        $this->post('/users', $credentials, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);
    }

    /**
     * Tests App\Controllers\UsersController::store() resource method.
     *
     * @depends testCsrfMiddleWareBehaviorForNonApiHandling
     */
    public function testUsersStoreForSuccess()
    {
        $credentials = ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'];
        $this->post('/users', $credentials, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(201);
        $this->seeJson([
            'email' => 'john.doe@example.com'
        ]);
        $this->seeInDatabase('users', ['email' => $credentials['email']]);
    }

    /**
     * Tests App\Controllers\UsersController::show() resource method.
     *
     * @depends testUsersStoreForSuccess
     */
    public function testUsersShowForSuccess()
    {
        $user = ['id' => 1, 'email' => 'john.doe@example.com'];
        $this->get('/users/' . $user['id'], self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson([
            'email' => 'john.doe@example.com'
        ]);
    }

    /**
     * Tests App\Controllers\UsersController::show() resource method.
     *
     * @depends testUsersStoreForSuccess
     */
    public function testUsersShowForExceptions()
    {
        # User doesn't exist
        $user = ['id' => 2, 'email' => 'john.doe@example.com'];
        $this->get('/users/' . $user['id'], self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(404);
        $this->seeJson(['exception' => NotFoundHttpException::class]);
    }

    /**
     * Tests App\Controllers\UsersController::edit() resource method.
     *
     * @depends testUsersShowForSuccess
     */
    public function testUsersEdit()
    {
        $this->get('/users/1/edit', self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->see('data');
        $this->seeJson(['message' => 'Ready']);
    }

    /**
     * Tests App\Controllers\UsersController::update() resource method.
     *
     * @depends testUsersShowForSuccess
     */
    public function testUsersUpdateForExceptions()
    {
        # User doesn't exist
        $user = [
            'id' => 2,
            'email' => 'john.doe@example.com',
            'old_password' => 'theWeakestPasswordEver',
            'password' => 's0m34ardPa55w0rd',
            'password_confirmation' => 's0m34ardPa55w0rd'
        ];
        $this->put('/users/' . $user['id'], array_except($user, ['id']), self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(404);
        $this->seeJson(['exception' => NotFoundHttpException::class]);

        # Validation fails: Email missing
        $user = [
            'id' => 1,
            'old_password' => 'theWeakestPasswordEver',
            'password' => 's0m34ardPa55w0rd',
            'password_confirmation' => 's0m34ardPa55w0rd'
        ];
        $this->put('/users/' . $user['id'], array_except($user, ['id']), self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);
    }

    /**
     * Tests App\Controllers\UsersController::update() resource method.
     *
     * @depends testUsersShowForSuccess
     */
    public function testUsersUpdateForSuccess()
    {
        $user = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'old_password' => 'theWeakestPasswordEver',
            'password' => 's0m34ardPa55w0rd',
            'password_confirmation' => 's0m34ardPa55w0rd'
        ];
        $this->put('/users/' . $user['id'], array_except($user, ['id']), self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['message' => 'Updated']);
    }

    /**
     * Tests App\Controllers\Users\PasswordController::sendPasswordResetLink() controller method.
     *
     * @depends testUsersUpdateForSuccess
     */
    public function testPasswordSendPasswordResetLinkForExceptions()
    {
        # Validation failure: Invalid email address
        $userData = ['email' => 'jane.doe@'];
        $this->post('/password/email', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);

        # User doesn't exist
        $userData = ['email' => 'jane.doe@example.com'];
        $this->post('/password/email', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(404);
        $this->seeJson(['exception' => NotFoundHttpException::class]);
    }

    /**
     * Tests App\Controllers\Users\PasswordController::sendPasswordResetLink() controller method.
     *
     * @depends testUsersUpdateForSuccess
     */
    public function testPasswordSendPasswordResetLinkForSuccess()
    {
        $userData = ['email' => 'john.doe@example.com'];
        $this->post('/password/email', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['message' => trans('passwords.sent')]);
    }

    /**
     * Tests App\Controllers\Users\PasswordController::resetPassword() controller method.
     *
     * @depends testPasswordSendPasswordResetLinkForSuccess
     */
    public function testPasswordResetPasswordForExceptions()
    {
        $passwordResetToken = app('db')->table('password_resets')->where('email', '=', 'john.doe@example.com')->value('token');

        # Validation failure: Token missing
        $userData = [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0'
        ];
        $this->post('/password/reset', $userData, self::$requestHeaders);
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
        $this->post('/password/reset', $userData, self::$requestHeaders);
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
        $this->post('/password/reset', $userData, self::$requestHeaders);
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
        $this->post('/password/reset', $userData, self::$requestHeaders);
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
        $this->post('/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => TokenNotValidException::class]);
    }

    /**
     * Tests App\Controllers\Users\PasswordController::resetPassword() controller method.
     *
     * @depends testPasswordSendPasswordResetLinkForSuccess
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
        $this->post('/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['message' => trans('passwords.reset')]);
    }

    /**
     * Tests App\Controllers\Users\AuthController::postLogin() controller method.
     *
     * @depend testPasswordResetPasswordForSuccess
     */
    public function testAuthLoginForExceptions()
    {
        # Validation failure: Email address missing
        $user = ['password' => 's0m34ardPa55w0rd'];
        $this->post('/login', $user, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);
        $this->assertTrue(app('auth.driver')->guest());

        # Validation failure: Password missing
        $user = ['email' => 'john.doe@example.com'];
        $this->post('/login', $user, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => ValidationException::class]);
        $this->assertTrue(app('auth.driver')->guest());

        # Invalid login credentials
        $user = ['email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'];
        $this->post('/login', $user, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => LoginNotValidException::class]);
        $this->assertTrue(app('auth.driver')->guest());
    }

    /**
     * Tests App\Controllers\Users\AuthController::postLogin() controller method.
     *
     * @depends testPasswordResetPasswordForSuccess
     */
    public function testAuthLoginForSuccess()
    {
        $user = ['email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rdV3r510nTw0'];
        $this->post('/login', $user, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['message' => 'Login successful']);
        $this->assertFalse(app('auth.driver')->guest());
    }

    /**
     * Tests App\Controllers\Users\ActivationController::requestActivationCode() controller method.
     *
     * @depends testAuthLoginForSuccess
     */
    public function testActivationRequestActivationCodeForSuccess()
    {
        app('auth.driver')->loginUsingId(1);
        $this->assertFalse(app('auth.driver')->guest());
        $this->assertTrue(app('auth.driver')->check());

        $this->get('/activation', self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['message' => 'Activation link sent']);
    }

    /**
     * Tests App\Controllers\Users\ActivationController::activate() controller method.
     *
     * @depends testActivationRequestActivationCodeForSuccess
     */
    public function testActivationWebActivateForExceptions()
    {
        $this->get('/activation/wrong-token', self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(405);
        $this->seeJson(['exception' => NotImplementedException::class]);
    }

    /**
     * Tests App\Controllers\Users\ActivationController::activate() controller method.
     *
     * @depends testActivationRequestActivationCodeForSuccess
     */
    public function testActivationJsonActivateForExceptions()
    {
        $this->post('/activation', ['token' => 'wrong-token'], self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(422);
        $this->seeJson(['exception' => TokenNotValidException::class]);
    }

    /**
     * Tests App\Controllers\Users\ActivationController::activate() controller method.
     *
     * @depends testActivationRequestActivationCodeForSuccess
     */
    public function testActivationJsonActivateForSuccess()
    {
        $data = ['token' => app('db')->table('users_activations')->where('user_id', '=', 1)->value('code')];
        $this->post('/activation', $data, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
        $this->seeJson(['message' => 'Activated']);
    }

    /**
     * Tests App\Controllers\Users\ActivationController::requestActivationCode() controller method.
     *
     * @depends testActivationRequestActivationCodeForSuccess
     */
    public function testActivationRequestActivationCodeForExceptions()
    {
        # Failure: Tries to ask for code without logging in.
        $this->assertTrue(app('auth.driver')->guest());
        $this->get('/activation', self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(401);
        $this->seeJson(['exception' => UnauthorizedHttpException::class]);

        # Failure: Tries to ask for code when user is already activated.
        app('auth.driver')->loginUsingId(1);
        $this->assertFalse(app('auth.driver')->guest());
        $this->assertTrue(app('auth.driver')->check());
        $this->get('/activation', self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        $this->seeStatusCode(500);
        $this->seeJson(['exception' => UserAlreadyActivatedException::class]);
    }

    /**
     * Tests App\Controllers\Users\AuthController::getLogout() controller method.
     *
     * @depends testAuthLoginForSuccess
     */
    public function testAuthLogout()
    {
        app('auth.driver')->loginUsingId(1);
        $this->assertFalse(app('auth.driver')->guest());
        $this->assertTrue(app('auth.driver')->check());

        $this->get('logout', self::$requestHeaders);
        $this->assertTrue(app('auth.driver')->guest());
        $this->assertFalse(app('auth.driver')->check());
        $this->shouldReturnJson();
        $this->seeStatusCode(200);
    }

    /**
     * Tests App\Controllers\UsersController::destroy() resource method.
     *
     * @depends testAuthLoginForSuccess
     */
    public function testUsersDestroyForExceptions()
    {
        # Invalid password provided
        $user = ['id' => 1, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'];
        $this->delete('/users/' . $user['id'], array_except($user, ['email']), self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(422);
        $this->see('message');
        $this->seeJson(['exception' => PasswordNotValidException::class]);

        # User doesn't exist
        $user = ['id' => 2, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'];
        $this->delete('/users/' . $user['id'], array_except($user, ['email']), self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(404);
        $this->see('message');
        $this->seeJson(['exception' => NotFoundHttpException::class]);
    }

    /**
     * Tests App\Controllers\UsersController::destroy() resource method.
     *
     * @depends testAuthLoginForSuccess
     */
    public function testUsersDestroyForSuccess()
    {
        $user = ['id' => 1, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rdV3r510nTw0'];
        $this->delete('/users/' . $user['id'], array_except($user, ['email']), self::$requestHeaders);
        $this->seeStatusCode(204);
    }
}

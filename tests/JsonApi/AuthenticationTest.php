<?php namespace App\Tests\JsonApi;

use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\LoginNotValidException;
use App\Exceptions\Users\PasswordNotValidException;
use App\Exceptions\Users\TokenNotValidException;
use App\Exceptions\Users\UserNotFoundException;
use App\Tests\IlluminateTestCase;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthenticationTest extends IlluminateTestCase
{
    /**
     * This is used to memorize password reset token for tests.
     *
     * @var string
     */
    public static $passwordResetToken;

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
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

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

    public function data_testStore()
    {
        return [
            [ // Validation fail: password_confirmation missing
                ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver'],
                ValidationException::class
            ],
            [ // Validation fail: email missing
                ['password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'],
                ValidationException::class
            ],
            [
                ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'],
                ''
            ]
        ];
    }

    /**
     * Tests App\Controllers\UsersController::store() resource method.
     *
     * @dataProvider data_testStore
     *
     * @param array  $credentials
     * @param string $exceptionExpected
     */
    public function testStore(array $credentials, $exceptionExpected = '')
    {
        $this->post('/users', $credentials, self::$requestHeaders);
        $this->shouldReturnJson();
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case ValidationException::class:
                    $this->seeStatusCode(422);
                    break;
                default:
                    $this->seeStatusCode(500);
                    break;
            }
            $this->seeJson(['exception' => $exceptionExpected]);
            isset($credentials['email']) && $this->notSeeInDatabase('users', ['email' => $credentials['email']]);
        } else {
            $this->seeStatusCode(200);
            $this->seeJson(['message' => 'Created']);
            $this->seeInDatabase('users', ['email' => $credentials['email']]);
        }
    }

    public function data_testShow()
    {
        return [
            [
                ['id' => 1, 'email' => 'john.doe@example.com'],
                ''
            ],
            [
                ['id' => 2, 'email' => 'john.doe@example.com'],
                ModelNotFoundException::class
            ]
        ];
    }

    /**
     * Tests App\Controllers\UsersController::show() resource method.
     *
     * @dataProvider data_testShow
     * @depends      testStore
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testShow(array $user, $exceptionExpected)
    {
        $this->get('/users/' . $user['id'], self::$requestHeaders);
        $this->shouldReturnJson();
        if (!empty($exceptionExpected)) {
            $this->seeStatusCode(404);
            $this->seeJson(['exception' => $exceptionExpected]);
        } else {
            $this->seeStatusCode(200);
            $this->see('data');
            $this->see('john.doe@example.com');
        }
    }

    public function data_testUpdate()
    {
        return [
            [
                [ // Wrong old_password
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'old_password' => 'someWrongPassword',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ],
                PasswordNotValidException::class
            ],
            [
                [ // Validation fail: password_confirmation missing
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'old_password' => 'theWeakestPasswordEver',
                    'password' => 's0m34ardPa55w0rd'
                ],
                ValidationException::class
            ],
            [
                [ // Validation fail: old_password missing
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ],
                ValidationException::class
            ],
            [
                [ // Non-existing user
                    'id' => 2,
                    'email' => 'john.doe@example.com',
                    'old_password' => 'theWeakestPasswordEver',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ],
                ModelNotFoundException::class
            ],
            [
                [ // Correct data
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'old_password' => 'theWeakestPasswordEver',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ],
                ''
            ]
        ];
    }

    /**
     * Tests App\Controllers\UsersController::update() resource method.
     *
     * @dataProvider data_testUpdate
     * @depends      testStore
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testUpdate(array $user, $exceptionExpected)
    {
        $parameters = array_except($user, ['id']);

        $this->put('/users/' . $user['id'], $parameters, self::$requestHeaders);
        $this->shouldReturnJson();
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case ModelNotFoundException::class:
                    $this->seeStatusCode(404);
                    break;
                case PasswordNotValidException::class:
                case ValidationException::class:
                    $this->seeStatusCode(422);
                    break;
                default:
                    $this->seeStatusCode(500);
                    break;
            }
            $this->seeJson(['exception' => $exceptionExpected]);
        } else {
            $this->seeStatusCode(200);
            $this->seeJson(['message' => 'Updated']);
        }
    }

    public function data_testPostEmail()
    {
        return [
            [
                [ // Invalid email
                    'email' => 'jane.doe@'
                ],
                ValidationException::class
            ],
            [
                [ // Wrong email
                    'email' => 'jane.doe@example.com'
                ],
                UserNotFoundException::class
            ],
            [
                [ // Correct data
                    'email' => 'john.doe@example.com'
                ],
                ''
            ]
        ];
    }

    /**
     * Tests App\Controllers\Users\PasswordController::postEmail() controller method.
     *
     * @dataProvider data_testPostEmail
     * @depends      testUpdate
     * @large
     *
     * @param array  $userData
     * @param string $exceptionExpected
     */
    public function testPostEmail(array $userData, $exceptionExpected)
    {
        $this->post('/password/email', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case UserNotFoundException::class:
                    $this->seeStatusCode(404);
                    break;
                case ValidationException::class:
                    $this->seeStatusCode(422);
                    break;
                default:
                    $this->seeStatusCode(500);
                    break;
            }
            $this->seeJson(['exception' => $exceptionExpected]);
        } else {
            $this->seeStatusCode(200);

            self::$passwordResetToken = \DB::table('password_resets')->where('email', '=', 'john.doe@example.com')->value('token');
            $this->seeJson(['message' => trans(PasswordBroker::RESET_LINK_SENT)]);
        }
    }

    public function data_testPostReset()
    {
        return [
            [ // Validation fail: 'token' missing
                ['id' => 1, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rdV3r510nTw0', 'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0'],
                ValidationException::class
            ],
            [ // Validation fail: Password confirmation mismatch
                ['id' => 1, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rdV3r510nTw0', 'password_confirmation' => 's0m34ardPa55w0rd', 'token' => self::$passwordResetToken],
                ValidationException::class
            ],
            [ // Validation fail: Password confirmation missing
                ['id' => 1, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rdV3r510nTw0', 'token' => self::$passwordResetToken],
                ValidationException::class
            ],
            [ // Wrong email/account supplied
                [
                    'id' => 2,
                    'email' => 'jane.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => self::$passwordResetToken
                ],
                UserNotFoundException::class
            ],
            [ // Wrong token supplied
                [
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => 'wrong-token'
                ],
                TokenNotValidException::class
            ],
            [ // Correct entry
                [
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => self::$passwordResetToken
                ],
                ''
            ]
        ];
    }

    /**
     * Tests App\Controllers\Users\PasswordController::postReset() controller method.
     *
     * @dataProvider data_testPostReset
     * @depends      testPostEmail
     *
     * @param array  $userData
     * @param string $exceptionExpected
     */
    public function testPostReset(array $userData, $exceptionExpected)
    {
        // Fixing data with valid tokens
        if ($exceptionExpected != TokenNotValidException::class and array_key_exists('token', $userData)) {
            $userData['token'] = self::$passwordResetToken;
        }

        $this->post('/password/reset', $userData, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case UserNotFoundException::class:
                case NotFoundHttpException::class:
                    $this->seeStatusCode(404);
                    break;
                case ValidationException::class:
                case TokenNotValidException::class:
                    $this->seeStatusCode(422);
                    break;
                default:
                    $this->seeStatusCode(500);
                    break;
            }
            $this->seeJson(['exception' => $exceptionExpected]);
        } else {
            $this->seeStatusCode(200);
        }
    }

    public function data_testLogin()
    {
        return [
            [
                ['password' => 's0m34ardPa55w0rd'],
                ValidationException::class
            ],
            [
                ['email' => 'john.doe@example.com'],
                ValidationException::class
            ],
            [
                ['email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'],
                LoginNotValidException::class
            ],
            [
                ['email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rdV3r510nTw0'],
                ''
            ]
        ];
    }

    /**
     * Tests App\Controllers\Users\AuthController::postLogin() controller method.
     *
     * @dataProvider data_testLogin
     * @depends      testPostReset
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testLogin(array $user, $exceptionExpected)
    {
        $this->post('/login', $user, self::$requestHeaders);

        $this->shouldReturnJson();

        $this->see('message');

        if (!empty($exceptionExpected)) {
            $this->seeStatusCode(422);
            $this->seeJson(['exception' => $exceptionExpected]);
            $this->assertFalse(\Auth::check());
        } else {
            $this->seeStatusCode(200);
            $this->assertTrue(\Auth::check());
        }
    }

    /**
     * Tests App\Controllers\Users\AuthController::getLogout() controller method.
     *
     * @depends testLogin
     */
    public function testLogout()
    {
        \Auth::loginUsingId(1);
        $this->assertTrue(\Auth::check());

        $this->get('logout', self::$requestHeaders);
        $this->shouldReturnJson();
        $this->seeStatusCode(200);

        $this->assertFalse(\Auth::check());
    }

    public function data_testDestroy()
    {
        return [
            [
                ['id' => 1, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'],
                PasswordNotValidException::class
            ],
            [
                ['id' => 2, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'],
                ModelNotFoundException::class
            ],
            [
                ['id' => 1, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rdV3r510nTw0'],
                ''
            ]
        ];
    }

    /**
     * Tests App\Controllers\UsersController::destroy() resource method.
     *
     * @dataProvider data_testDestroy
     * @depends      testPostReset
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testDestroy(array $user, $exceptionExpected)
    {
        $parameters = array_except($user, ['email']);

        $this->delete('/users/' . $user['id'], $parameters, self::$requestHeaders);
        $this->shouldReturnJson();
        $this->see('message');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case ModelNotFoundException::class:
                    $this->seeStatusCode(404);
                    break;
                case PasswordNotValidException::class:
                    $this->seeStatusCode(422);
                    break;
                default:
                    $this->seeStatusCode(500);
                    break;
            }
            $this->seeJson(['exception' => $exceptionExpected]);
        } else {
            $this->seeStatusCode(200);
            $this->seeJson(['message' => 'Deleted']);
        }
    }
}

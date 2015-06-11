<?php

use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\LoginNotValidException;
use App\Exceptions\Users\PasswordNotValidException;
use App\Exceptions\Users\TokenNotValidException;
use App\Exceptions\Users\UserNotFoundException;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersControllerJsonApiTest extends TestCase
{
    use WithoutMiddleware;

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
        static::createApplication();

        Artisan::call('migrate:refresh');
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
        $crawler = $this->post('/users', $credentials, self::$requestHeaders);
        $crawler->shouldReturnJson();
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case ValidationException::class:
                    $crawler->seeStatusCode(422);
                    break;
                default:
                    $crawler->seeStatusCode(500);
                    break;
            }
            $crawler->seeJson(['exception' => $exceptionExpected]);
        } else {
            $crawler->seeStatusCode(200);
            $crawler->seeJson(['message' => 'Created']);
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
                NotFoundHttpException::class
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
        $crawler = $this->get('/users/'.$user['id'], self::$requestHeaders);
        $crawler->shouldReturnJson();
        if (!empty($exceptionExpected)) {
            $crawler->seeStatusCode(404);
            $crawler->seeJson(['exception' => $exceptionExpected]);
        } else {
            $crawler->seeStatusCode(200);
            $crawler->see('data');
            $crawler->see('john.doe@example.com');
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
                NotFoundHttpException::class
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

        $crawl = $this->put('/users/'.$user['id'], $parameters, self::$requestHeaders);
        $crawl->shouldReturnJson();
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case NotFoundHttpException::class:
                    $crawl->seeStatusCode(404);
                    break;
                case PasswordNotValidException::class:
                case ValidationException::class:
                    $crawl->seeStatusCode(422);
                    break;
                default:
                    $crawl->seeStatusCode(500);
                    break;
            }
            $crawl->seeJson(['exception' => $exceptionExpected]);
        } else {
            $crawl->seeStatusCode(200);
            $crawl->seeJson(['message' => 'Updated']);
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
                NotFoundHttpException::class
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
     * Tests PasswordController::postEmail() controller method.
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
        $crawl = $this->post('/password/email', $userData, self::$requestHeaders);
        $crawl->shouldReturnJson();
        $crawl->see('message');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case NotFoundHttpException::class:
                    $crawl->seeStatusCode(404);
                    break;
                case ValidationException::class:
                    $crawl->seeStatusCode(422);
                    break;
                default:
                    $crawl->seeStatusCode(500);
                    break;
            }
            $crawl->seeJson(['exception' => $exceptionExpected]);
        } else {
            $crawl->seeStatusCode(200);

            self::$passwordResetToken = \DB::table('password_resets')->where('email', '=', 'john.doe@example.com')->value('token');
            $crawl->seeJson(['message' => trans(PasswordBroker::RESET_LINK_SENT)]);
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
     * Tests PasswordController::postReset() controller method.
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

        $crawl = $this->post('/password/reset', $userData, self::$requestHeaders);
        $crawl->shouldReturnJson();
        $crawl->see('message');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case UserNotFoundException::class:
                case NotFoundHttpException::class:
                    $crawl->seeStatusCode(404);
                    break;
                case ValidationException::class:
                case TokenNotValidException::class:
                    $crawl->seeStatusCode(422);
                    break;
                default:
                    $crawl->seeStatusCode(500);
                    break;
            }
            $crawl->seeJson(['exception' => $exceptionExpected]);
        } else {
            $crawl->seeStatusCode(200);
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
     * Tests AuthController::postLogin() controller method.
     *
     * @dataProvider data_testLogin
     * @depends      testPostReset
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testLogin(array $user, $exceptionExpected)
    {
        $crawl = $this->post('/login', $user, self::$requestHeaders);

        $crawl->shouldReturnJson();

        $crawl->see('message');

        if (!empty($exceptionExpected)) {
            $crawl->seeStatusCode(422);
            $crawl->seeJson(['exception' => $exceptionExpected]);
            $this->assertFalse(\Auth::check());
        } else {
            $crawl->seeStatusCode(200);
            $this->assertTrue(\Auth::check());
        }
    }

    /**
     * Tests AuthController::getLogout() controller method.
     *
     * @depends testLogin
     */
    public function testLogout()
    {
        \Auth::loginUsingId(1);
        $this->assertTrue(\Auth::check());

        $crawl = $this->get('logout', self::$requestHeaders);
        $crawl->shouldReturnJson();
        $crawl->seeStatusCode(200);

        $this->assertFalse(Auth::check());
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
                NotFoundHttpException::class
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

        $crawl = $this->delete('/users/'.$user['id'], $parameters, self::$requestHeaders);
        $crawl->shouldReturnJson();
        $crawl->see('message');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case NotFoundHttpException::class:
                    $crawl->seeStatusCode(404);
                    break;
                case PasswordNotValidException::class:
                    $crawl->seeStatusCode(422);
                    break;
                default:
                    $crawl->seeStatusCode(500);
                    break;
            }
            $crawl->seeJson(['exception' => $exceptionExpected]);
        } else {
            $crawl->seeStatusCode(200);
            $crawl->seeJson(['message' => 'Deleted']);
        }
    }
}

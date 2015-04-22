<?php

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class UsersControllerTest extends TestCase
{
    public static $csrfToken = null;

    public static $passwordResetToken = null;

    public static $requestHeaders = [
        'HTTP_ACCEPT' => 'application/json'
    ];

    public static function setUpBeforeClass()
    {
        // static::createApplication(); TODO Facilitate this method @see https://github.com/laravel/framework/pull/8496
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        Artisan::call('migrate:refresh');
    }

    public function setUp()
    {
        parent::setUp();

        switch ($this->getName()) {
            case 'testStore with data set #0':
            case 'testStore with data set #1':
            case 'testStore with data set #2':
                $handshakeRequestEndpointUrl = '/users/create';
                break;
            case 'testUpdate with data set #0':
            case 'testUpdate with data set #1':
            case 'testUpdate with data set #2':
            case 'testUpdate with data set #3':
            case 'testUpdate with data set #4':
                $handshakeRequestEndpointUrl = '/users/1/edit';
                break;
            case 'testDestroy with data set #0':
            case 'testDestroy with data set #1':
            case 'testDestroy with data set #2':
                $handshakeRequestEndpointUrl = '/users/1';
                break;
            case 'testPostEmail with data set #0':
            case 'testPostEmail with data set #1':
            case 'testPostEmail with data set #2':
                $handshakeRequestEndpointUrl = '/password/email';
                break;
            case 'testPostReset with data set #0':
            case 'testPostReset with data set #1':
            case 'testPostReset with data set #2':
            case 'testPostReset with data set #3':
            case 'testPostReset with data set #4':
            case 'testPostReset with data set #5':
            case 'testPostReset with data set #6':
                $handshakeRequestEndpointUrl = '/password/reset/' . self::$passwordResetToken;
                break;
            case 'testLogin with data set #0':
            case 'testLogin with data set #1':
            case 'testLogin with data set #2':
            case 'testLogin with data set #3':
                $handshakeRequestEndpointUrl = '/auth/login';
                break;
            default:
                return;
        }

        $handshakeRequestToGrabCsrfToken = $this->call('GET', $handshakeRequestEndpointUrl);
        /**
         * @var \Symfony\Component\HttpFoundation\Cookie[] $responseCookiesFromHandshakeRequest
         */
        self::$csrfToken = null;
        $responseCookiesFromHandshakeRequest = $handshakeRequestToGrabCsrfToken->headers->getCookies(ResponseHeaderBag::COOKIES_FLAT);
        foreach ($responseCookiesFromHandshakeRequest as $cookie) {
            $cookie->getName() == 'XSRF-TOKEN' && self::$csrfToken = $cookie->getValue();
        }

        !is_null(self::$csrfToken) and self::$requestHeaders = array_merge(self::$requestHeaders, array('HTTP_X_XSRF_TOKEN' => self::$csrfToken));
    }

    public function data_testStore()
    {
        return array(
            array( // Validation fail: password_confirmation missing
                array('email' => 'shehi@imanov.me', 'password' => 'theWeakestPasswordEver'),
                'ValidationException'
            ),
            array( // Validation fail: email missing
                array('password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'),
                'ValidationException'
            ),
            array(
                array('email' => 'shehi@imanov.me', 'password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'),
                ''
            )
        );
    }

    /**
     * @dataProvider data_testStore
     *
     * @param array  $credentials
     * @param string $exceptionExpected
     */
    public function testStore(array $credentials, $exceptionExpected = '')
    {
        $response = $this->call('POST', '/users', $credentials, array(), array(), self::$requestHeaders);
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, "Response object needs to have a 'message' field.");
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case 'ValidationException':
                    $this->assertResponseStatus(422);
                    break;
                default:
                    $this->assertResponseStatus(500);
                    break;
            }
            $this->assertObjectHasAttribute('exception', $responseAsObject, "Response object needs to have a 'exception' field.");
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
        } else {
            $this->assertResponseStatus(200);
            $this->assertEquals('Created', $responseAsObject->message, 'Response message should be \'User created\'.');
        }
    }

    public function data_testShow()
    {
        return array(
            array(
                array('id' => 1, 'email' => 'shehi@imanov.me'),
                ''
            ),
            array(
                array('id' => 2, 'email' => 'shehi@imanov.me'),
                'NotFoundHttpException'
            )
        );
    }

    /**
     * @dataProvider data_testShow
     * @depends      testStore
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testShow(array $user, $exceptionExpected)
    {
        $response = $this->call('GET', '/users/' . $user['id'], array(), array(), array(), self::$requestHeaders);
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            $this->assertResponseStatus(404);
            $this->assertObjectHasAttribute('exception', $responseAsObject, 'Response object needs to have a \'exception\' field.');
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
        } else {
            $this->assertResponseStatus(200);
            $this->assertObjectHasAttribute('data', $responseAsObject, 'Response object needs to have a \'data\' field.');
            $_responseObjectDataAttribute = json_decode($responseAsObject->data);
            $this->assertEquals($user['email'], $_responseObjectDataAttribute->email, 'User email should be equal to \'shehi@imanov.me\'.');
        }
    }

    public function data_testUpdate()
    {
        return array(
            array(
                array( // Wrong old_password
                    'id' => 1,
                    'email' => 'shehi@imanov.me',
                    'old_password' => 'someWrongPassword',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ),
                'PasswordNotValidException'
            ),
            array(
                array( // Validation fail: password_confirmation missing
                    'id' => 1,
                    'email' => 'shehi@imanov.me',
                    'old_password' => 'theWeakestPasswordEver',
                    'password' => 's0m34ardPa55w0rd'
                ),
                'ValidationException'
            ),
            array(
                array( // Validation fail: old_password missing
                    'id' => 1,
                    'email' => 'shehi@imanov.me',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ),
                'ValidationException'
            ),
            array(
                array( // Non-existing user
                    'id' => 2,
                    'email' => 'shehi@imanov.me',
                    'old_password' => 'theWeakestPasswordEver',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ),
                'NotFoundHttpException'
            ),
            array(
                array( // Correct data
                    'id' => 1,
                    'email' => 'shehi@imanov.me',
                    'old_password' => 'theWeakestPasswordEver',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ),
                ''
            )
        );
    }

    /**
     * @dataProvider data_testUpdate
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testUpdate(array $user, $exceptionExpected)
    {
        $parameters = array_except($user, ['id']);
        $response = $this->call('PUT', '/users/' . $user['id'], $parameters, array(), array(), self::$requestHeaders);
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case 'NotFoundHttpException':
                    $this->assertResponseStatus(404);
                    break;
                case 'PasswordNotValidException':
                case 'ValidationException':
                    $this->assertResponseStatus(422);
                    break;
                default:
                    $this->assertResponseStatus(500);
                    break;
            }
            $this->assertObjectHasAttribute('exception', $responseAsObject, 'Response object needs to have a \'exception\' field.');
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
        } else {
            $this->assertResponseStatus(200);
            $this->assertEquals('Updated', $responseAsObject->message, 'Response message should be \'Updated\'.');
        }
    }

    public function data_testPostEmail()
    {
        return array(
            array(
                array( // Invalid email
                    'email' => 'john.doe@'
                ),
                'ValidationException'
            ),
            array(
                array( // Wrong email
                    'email' => 'john.doe@example.com'
                ),
                'NotFoundHttpException'
            ),
            array(
                array( // Correct data
                    'email' => 'shehi@imanov.me'
                ),
                ''
            )
        );
    }

    /**
     * @dataProvider data_testPostEmail
     * @large
     *
     * @param array  $userData
     * @param string $exceptionExpected
     */
    public function testPostEmail(array $userData, $exceptionExpected)
    {
        $response = $this->call('POST', '/password/email', $userData, array(), array(), self::$requestHeaders);
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case 'NotFoundHttpException':
                    $this->assertResponseStatus(404);
                    break;
                case 'ValidationException':
                    $this->assertResponseStatus(422);
                    break;
                default:
                    $this->assertResponseStatus(500);
                    break;
            }
            $this->assertObjectHasAttribute('exception', $responseAsObject, 'Response object needs to have a \'exception\' field.');
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
        } else {
            $this->assertResponseStatus(200);
            $this->assertNotEmpty(self::$passwordResetToken = $responseAsObject->token, 'Response must include non-empty password reset token.');
        }
    }

    /**
     * @depends testPostEmail
     */
    public function data_testPostReset()
    {
        return array(
            array( // Validation fail: 'token' missing
                array('id' => 1, 'email' => 'shehi@imanov.me', 'password' => 's0m34ardPa55w0rdV3r510nTw0', 'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0'),
                'ValidationException'
            ),
            array( // Validation fail: Password confirmation mismatch
                array('id' => 1, 'email' => 'shehi@imanov.me', 'password' => 's0m34ardPa55w0rdV3r510nTw0', 'password_confirmation' => 's0m34ardPa55w0rd', 'token' => self::$passwordResetToken),
                'ValidationException'
            ),
            array( // Validation fail: Password confirmation missing
                array('id' => 1, 'email' => 'shehi@imanov.me', 'password' => 's0m34ardPa55w0rdV3r510nTw0', 'token' => self::$passwordResetToken),
                'ValidationException'
            ),
            array( // Wrong email/account supplied
                array(
                    'id' => 2,
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => self::$passwordResetToken
                ),
                'NotFoundHttpException'
            ),
            array( // Wrong token supplied
                array(
                    'id' => 1,
                    'email' => 'shehi@imanov.me',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => 'wrong-token'),
                'TokenNotValidException'
            ),
            array( // Correct entry
                array(
                    'id' => 1,
                    'email' => 'shehi@imanov.me',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => self::$passwordResetToken
                ),
                ''
            )
        );
    }

    /**
     * @dataProvider data_testPostReset
     *
     * @param array  $userData
     * @param string $exceptionExpected
     */
    public function testPostReset(array $userData, $exceptionExpected)
    {
        // Fixing data with valid tokens
        if ($exceptionExpected != 'TokenNotValidException' and array_key_exists('token', $userData)) {
            $userData['token'] = self::$passwordResetToken;
        }

        $response = $this->call('POST', '/password/reset', $userData, array(), array(), self::$requestHeaders);
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case 'NotFoundHttpException':
                    $this->assertResponseStatus(404);
                    break;
                case 'ValidationException':
                case 'TokenNotValidException':
                    $this->assertResponseStatus(422);
                    break;
                default:
                    $this->assertResponseStatus(500);
                    break;
            }
            $this->assertObjectHasAttribute('exception', $responseAsObject, 'Response object needs to have a \'exception\' field.');
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
        } else {
            $this->assertResponseStatus(200);
        }
    }

    public function data_testLogin()
    {
        return array(
            array(
                array('password' => 's0m34ardPa55w0rd'),
                'ValidationException'
            ),
            array(
                array('email' => 'shehi@imanov.me'),
                'ValidationException'
            ),
            array(
                array('email' => 'shehi@imanov.me', 'password' => 's0m34ardPa55w0rd'),
                'LoginNotValidException'
            ),
            array(
                array('email' => 'shehi@imanov.me', 'password' => 's0m34ardPa55w0rdV3r510nTw0'),
                ''
            )
        );
    }

    /**
     * @dataProvider data_testLogin
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testLogin(array $user, $exceptionExpected)
    {
        $response = $this->call('POST', '/auth/login', $user, array(), array(), self::$requestHeaders);
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            $this->assertResponseStatus(422);
            $this->assertObjectHasAttribute('exception', $responseAsObject, 'Response object needs to have a \'exception\' field.');
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
            $this->assertFalse(Auth::check());
        } else {
            $this->assertResponseStatus(200);
            $this->assertTrue(Auth::check());
        }
    }

    /**
     * @depends testLogin
     */
    public function testLogout()
    {
        Auth::loginUsingId(1);
        $this->assertTrue(Auth::check());

        $response = $this->call('GET', '/auth/logout', array(), array(), array(), self::$requestHeaders);
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);

        $this->assertResponseStatus(200);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertNotNull($responseAsObject);

        $this->assertFalse(Auth::check());
    }

    public function data_testDestroy()
    {
        return array(
            array(
                array('id' => 1, 'email' => 'shehi@imanov.me', 'password' => 's0m34ardPa55w0rd'),
                'PasswordNotValidException'
            ),
            array(
                array('id' => 2, 'email' => 'shehi@imanov.me', 'password' => 's0m34ardPa55w0rd'),
                'NotFoundHttpException'
            ),
            array(
                array('id' => 1, 'email' => 'shehi@imanov.me', 'password' => 's0m34ardPa55w0rdV3r510nTw0'),
                ''
            )
        );
    }

    /**
     * @dataProvider data_testDestroy
     *
     * @param array  $user
     * @param string $exceptionExpected
     */
    public function testDestroy(array $user, $exceptionExpected)
    {
        $parameters = array_except($user, ['email']);
        $response = $this->call('DELETE', '/users/' . $user['id'], $parameters, array(), array(), self::$requestHeaders);
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case 'NotFoundHttpException':
                    $this->assertResponseStatus(404);
                    break;
                case 'PasswordNotValidException':
                    $this->assertResponseStatus(422);
                    break;
                default:
                    $this->assertResponseStatus(500);
                    break;
            }
            $this->assertObjectHasAttribute('exception', $responseAsObject, 'Response object needs to have a \'exception\' field.');
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
        } else {
            $this->assertResponseStatus(200);
            $this->assertEquals('Deleted', $responseAsObject->message, 'Response message should be \'Deleted\'.');
        }
    }
}

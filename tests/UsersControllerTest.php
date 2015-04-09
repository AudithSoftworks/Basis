<?php

use \Symfony\Component\HttpFoundation\ResponseHeaderBag;

class UsersControllerTest extends TestCase
{
    public static $csrfToken = null;

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
            case 'testUpdate with data set #2':
            case 'testUpdate with data set #3':
            case 'testUpdate with data set #4':
                $handshakeRequestEndpointUrl = '/users/1/edit';
                break;
            case 'testUpdate with data set #1':
                $handshakeRequestEndpointUrl = '/users/2/edit';
                break;
            case 'testDestroy with data set #0':
            case 'testDestroy with data set #2':
                $handshakeRequestEndpointUrl = '/users/1';
                break;
            case 'testDestroy with data set #1':
                $handshakeRequestEndpointUrl = '/users/2';
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
    }

    public function data_testStore()
    {
        return array(
            array(
                array('email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'),
                ''
            ),
            array( // Validation fail: password_confirmation missing
                array('email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver'),
                'HttpResponseException'
            ),
            array( // Validation fail: email missing
                array('password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'),
                'HttpResponseException'
            ),
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
        $response = $this->call('POST', '/users', $credentials, array(), array(), !self::$csrfToken ? array() : array('HTTP_X_XSRF_TOKEN' => self::$csrfToken));
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case 'HttpResponseException':
                    $this->assertResponseStatus(422);
                    break;
                default:
                    $this->assertResponseStatus(500);
                    break;
            }
            $this->assertObjectHasAttribute('exception', $responseAsObject, 'Response object needs to have a \'exception\' field.');
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
        } else {
            $this->assertResponseStatus(201);
            $this->assertEquals('Created', $responseAsObject->message, 'Response message should be \'Created\'.');
        }
    }

    public function data_testShow()
    {
        return array(
            array(
                array('id' => 1, 'email' => 'john.doe@example.com'),
                ''
            ),
            array(
                array('id' => 2, 'email' => 'john.doe@example.com'),
                'UserNotFoundException'
            )
        );
    }

    /**
     * @dataProvider data_testShow
     * @depends      testStore
     *
     * @param array   $user
     * @param string  $exceptionExpected
     */
    public function testShow(array $user, $exceptionExpected)
    {
        $response = $this->call('GET', '/users/' . $user['id']);
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
            $this->assertEquals('Found', $responseAsObject->message, 'Response message should be \'Found\'.');
            $this->assertObjectHasAttribute('data', $responseAsObject, 'Response object needs to have a \'data\' field.');
            $_responseObjectDataAttribute = json_decode($responseAsObject->data);
            $this->assertEquals($user['email'], $_responseObjectDataAttribute->email, 'User email should be equal to \'john.doe@example.com\'.');
        }
    }

    public function data_testUpdate()
    {
        return array(
            array(
                array( // Wrong old_password
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'old_password' => 'someWrongPassword',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ),
                'PasswordNotValidException'
            ),
            array(
                array( // Validation fail: password_confirmation missing
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'old_password' => 'theWeakestPasswordEver',
                    'password' => 's0m34ardPa55w0rd'
                ),
                'HttpResponseException'
            ),
            array(
                array( // Validation fail: old_password missing
                    'id' => 1,
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ),
                'HttpResponseException'
            ),
            array(
                array( // Non-existing user
                    'id' => 2,
                    'email' => 'john.doe@example.com',
                    'old_password' => 'theWeakestPasswordEver',
                    'password' => 's0m34ardPa55w0rd',
                    'password_confirmation' => 's0m34ardPa55w0rd'
                ),
                'UserNotFoundException'
            ),
            array(
                array( // Correct data
                    'id' => 1,
                    'email' => 'john.doe@example.com',
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
        $response = $this->call('PUT', '/users/' . $user['id'], $parameters, array(), array(), !self::$csrfToken ? array() : array('HTTP_X_XSRF_TOKEN' => self::$csrfToken));
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            switch ($exceptionExpected) {
                case 'UserNotFoundException':
                    $this->assertResponseStatus(404);
                    break;
                case 'HttpResponseException':
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

    public function data_testDestroy()
    {
        return array(
            array(
                array('id' => 1, 'email' => 'john.doe@example.com', 'password' => 'someWrongPassword'),
                'PasswordNotValidException'
            ),
            array(
                array('id' => 2, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'),
                'UserNotFoundException'
            ),
            array(
                array('id' => 1, 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'),
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
        $response = $this->call('DELETE', '/users/' . $user['id'], $parameters, array(), array(), !self::$csrfToken ? array() : array('HTTP_X_XSRF_TOKEN' => self::$csrfToken));
        $responseRaw = $response->getContent();
        $responseAsObject = json_decode($responseRaw);
        $this->assertNotEmpty($responseRaw, 'Response needs to be not-empty.');
        $this->assertObjectHasAttribute('message', $responseAsObject, 'Response object needs to have a \'message\' field.');
        if (!empty($exceptionExpected)) {
            $this->assertResponseStatus(500);
            $this->assertObjectHasAttribute('exception', $responseAsObject, 'Response object needs to have a \'exception\' field.');
            $this->assertContains($exceptionExpected, $responseAsObject->exception);
        } else {
            $this->assertResponseStatus(200);
            $this->assertEquals('Deleted', $responseAsObject->message, 'Response message should be \'Deleted\'.');
        }
    }
}

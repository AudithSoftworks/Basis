<?php

use Illuminate\Contracts\Auth\PasswordBroker;

class AuthenticationWebUITest extends IlluminateTestCase
{
    /**
     * This is used to memorize password reset token for tests.
     *
     * @var string
     */
    public static $passwordResetToken;

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

    public function testHyperlinksInAuthenticationPages()
    {
        $this->visit('/register');
        $this->seeStatusCode(200);
        $this->click('Sign in');
        $this->seeStatusCode(200);
        $this->seePageIs('/login');
        $this->click('Register');
        $this->seeStatusCode(200);
        $this->seePageIs('/register');
        $this->click('Reset password');
        $this->seeStatusCode(200);
        $this->seePageIs('/password/email');
        $this->click('Sign in');
        $this->seeStatusCode(200);
        $this->seePageIs('/login');
        $this->click('Reset password');
        $this->seeStatusCode(200);
        $this->seePageIs('/password/email');
        $this->click('Register');
        $this->seeStatusCode(200);
        $this->seePageIs('/register');
    }

    public function data_testRegister()
    {
        return [
            [ // Password confirmation missing
                ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver']
            ],
            [ // Email missing
                ['name' => 'John Doe', 'password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver']
            ],
            [ // Short password
                ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'password' => 'short', 'password_confirmation' => 'short']
            ],
            [ // Success
                ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd', 'password_confirmation' => 's0m34ardPa55w0rd']
            ]
        ];
    }

    /**
     * Tests /register route.
     *
     * @dataProvider data_testRegister
     *
     * @param array $credentials
     */
    public function testRegister(array $credentials)
    {
        # Visit the page, make sure we have landed on right page.
        $this->visit('/register');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Create</b> a new account</h2>');

        # We shouldn't have flash error messages around.
        $this->see(trans('validation.required', ['attribute' => 'name']), true);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);

        # Fill in the form fed by data-provider
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }

        # Press the submit button
        $this->press('Sign up');

        # Check for errors
        switch ($this->getName()) {
            case 'testRegister with data set #0':
                $this->seePageIs('/register');
                $this->see('The password confirmation does not match.');
                $this->see(trans('validation.required', ['attribute' => 'name']), true);
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.required', ['attribute' => 'password']), true);
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);
                $this->notSeeInDatabase('users', ['email' => $credentials['email']]);
                break;
            case 'testRegister with data set #1':
                $this->seePageIs('/register');
                $this->see(trans('validation.required', ['attribute' => 'email']));
                $this->see(trans('validation.required', ['attribute' => 'name']), true);
                $this->see(trans('validation.required', ['attribute' => 'password']), true);
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);
                $this->notSeeInDatabase('users', ['name' => $credentials['name']]);
                break;
            case 'testRegister with data set #2':
                $this->seePageIs('/register');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.required', ['attribute' => 'name']), true);
                $this->see(trans('validation.required', ['attribute' => 'password']), true);
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]));
                $this->notSeeInDatabase('users', ['email' => $credentials['email']]);
                break;
            default:
                $this->seePageIs('/');
                $this->see('<b>Welcome</b>, John Doe!');
                $this->seeInDatabase('users', ['email' => $credentials['email']]);
                break;
        }
    }

    public function data_testLogin()
    {
        return [
            [ // Missing email
                ['password' => 's0m34ardPa55w0rd']
            ],
            [ // Missing password
                ['email' => 'john.doe@example.com']
            ],
            [ // Invalid login
                ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver'],
                'LoginNotValidException'
            ],
            [
                ['email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'],
                ''
            ]
        ];
    }

    /**
     * Tests /login route.
     *
     * @dataProvider data_testLogin
     *
     * @param array $credentials
     */
    public function testLogin(array $credentials)
    {
        # Visit the page, make sure we have landed on right page.
        $this->visit('/login');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Log in</b> to your account</h2>');

        # We shouldn't have flash error messages around.
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);

        # Fill in the form fed by data-provider
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }

        # Press the submit button
        $this->press('Log in');

        # Check for errors
        switch ($this->getName()) {
            case 'testLogin with data set #0':
                $this->seePageIs('/login');
                $this->see(trans('validation.required', ['attribute' => 'email']));
                $this->see(trans('validation.required', ['attribute' => 'password']), true);
                $this->see('These credentials do not match our records!', true);
                break;
            case 'testLogin with data set #1':
                $this->seePageIs('/login');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.required', ['attribute' => 'password']));
                $this->see('These credentials do not match our records!', true);
                break;
            case 'testLogin with data set #2':
                $this->seePageIs('/login');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.required', ['attribute' => 'password']), true);
                $this->see('These credentials do not match our records!');
                break;
            default:
                $this->seePageIs('/');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.required', ['attribute' => 'password']), true);
                $this->see('These credentials do not match our records!', true);
                $this->see('<b>Welcome</b>, John Doe!');
                break;
        }
    }

    /**
     * Tests /logout route.
     */
    public function testLogout()
    {
        /** @var App\Models\User $user */
        $user = \App\Models\User::find(1);
        $this->actingAs($user);
        $this->visit('/login');
        $this->seeStatusCode(200);
        $this->seePageIs(''); // Since we are authenticated, we are redirected to /
        $this->see('<h2><b>Welcome</b>, John Doe!</h2>');

        $this->click('Log out');
        $this->seeStatusCode(200);
        $this->seePageIs('/');
        $this->see('<h2><b>Welcome</b>!</h2>');
    }

    public function data_testPasswordEmail()
    {
        return [
            [
                [ // Missing email
                    'email' => ''
                ]
            ],
            [
                [ // Invalid email
                    'email' => 'john.doe@'
                ]
            ],
            [
                [ // Non-existent email
                    'email' => 'jane.doe@example.com'
                ]
            ],
            [
                [ // Correct data
                    'email' => 'john.doe@example.com'
                ]
            ]
        ];
    }

    /**
     * Tests /password/email route.
     *
     * @dataProvider data_testPasswordEmail
     *
     * @param array $credentials
     */
    public function testPasswordEmail(array $credentials)
    {
        # Visit the page, make sure we have landed on right page.
        $this->visit('/password/email');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Reset</b> your password</h2>');

        # We shouldn't have flash error messages around.
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans(PasswordBroker::INVALID_USER), true);
        $this->see(trans(PasswordBroker::RESET_LINK_SENT), true);

        # Fill in the form fed by data-provider
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }

        # Press the submit button
        $this->press('Send Password Reset Link');

        # Check for errors
        $this->seePageIs('/password/email');
        switch ($this->getName()) {
            case 'testPasswordEmail with data set #0':
                $this->see(trans('validation.required', ['attribute' => 'email']));
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans(PasswordBroker::INVALID_USER), true);
                $this->see(trans(PasswordBroker::RESET_LINK_SENT), true);
                break;
            case 'testPasswordEmail with data set #1':
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.email', ['attribute' => 'email']));
                $this->see(trans(PasswordBroker::INVALID_USER), true);
                $this->see(trans(PasswordBroker::RESET_LINK_SENT), true);
                break;
            case 'testPasswordEmail with data set #2':
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans(PasswordBroker::INVALID_USER));
                $this->see(trans(PasswordBroker::RESET_LINK_SENT), true);
                break;
            default:
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans(PasswordBroker::INVALID_USER), true);
                $this->see(trans(PasswordBroker::RESET_LINK_SENT));
                break;
        }
    }

    /**
     * Tests /password/reset route.
     */
    public function testPasswordResetWithMissingTokenSegmentInRequestUri()
    {
        # Visit the page, make sure we have landed on right page.
        $_uri = '/password/reset';
        $this->visit($_uri); // Token maybe missing in Request-URI

        # We shouldn't have flash error messages around.
        $this->seePageIs('/password/reset');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Reset</b> your password</h2>');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans(PasswordBroker::INVALID_USER), true);
        $this->see(trans(PasswordBroker::INVALID_TOKEN));
        $this->see(trans(PasswordBroker::PASSWORD_RESET), true);
        $this->see(trans(PasswordBroker::INVALID_PASSWORD, ['min_length' => \Config::get('auth.password.min_length')]), true);
    }

    public function data_testPasswordReset()
    {
        return [
            [ // Password confirmation mismatch
                [
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rd',
                    'token' => &self::$passwordResetToken
                ]
            ],
            [ // Password confirmation missing
                [
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => &self::$passwordResetToken
                ]
            ],
            [ // Wrong email/account supplied
                [
                    'email' => 'jane.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => &self::$passwordResetToken
                ]
            ],
            [ // Wrong token supplied
                [
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => 'wrong-token'
                ]
            ],
            [ // Short password
                [
                    'email' => 'john.doe@example.com',
                    'password' => 'short',
                    'password_confirmation' => 'short',
                    'token' => &self::$passwordResetToken
                ]
            ],
            [ // Correct entry
                [
                    'email' => 'john.doe@example.com',
                    'password' => 's0m34ardPa55w0rdV3r510nTw0',
                    'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0',
                    'token' => &self::$passwordResetToken
                ]
            ]
        ];
    }

    /**
     * Tests /password/reset/{token?} route.
     *
     * @dataProvider data_testPasswordReset
     *
     * @param array $credentials
     */
    public function testPasswordReset(array $credentials)
    {
        self::$passwordResetToken = \DB::table('password_resets')->where('email', '=', 'john.doe@example.com')->value('token');

        # Visit the page, make sure we have landed on right page.
        $_uri = '/password/reset';
        if (isset($credentials['token']) && !empty($credentials['token'])) {
            $_uri .= '/'.$credentials['token'];
        }
        $this->visit($_uri); // Token maybe missing in Request-URI
        $this->seeStatusCode(200);

        # We shouldn't have flash error messages around.
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans(PasswordBroker::INVALID_USER), true);
        $this->see(trans(PasswordBroker::INVALID_TOKEN), true);
        $this->see(trans(PasswordBroker::PASSWORD_RESET), true);
        $this->see(trans(PasswordBroker::INVALID_PASSWORD, ['min_length' => \Config::get('auth.password.min_length')]), true);

        # Fill in the form fed by data-provider
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }

        # Press the submit button
        $this->press('Reset Password');

        # Check for errors
        switch ($this->getName()) {
            case 'testPasswordReset with data set #0':
                $this->seePageIs($_uri);
                $this->see('<h2><b>Reset</b> your password</h2>');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans('validation.confirmed', ['attribute' => 'password']));
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);
                $this->see(trans(PasswordBroker::INVALID_USER), true);
                $this->see(trans(PasswordBroker::INVALID_TOKEN), true);
                $this->see(trans(PasswordBroker::PASSWORD_RESET), true);
                $this->see(trans(PasswordBroker::INVALID_PASSWORD, ['min_length' => \Config::get('auth.password.min_length')]), true);
                break;
            case 'testPasswordReset with data set #1':
                $this->seePageIs($_uri);
                $this->see('<h2><b>Reset</b> your password</h2>');
                $this->see('The email field is required.', true);
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans('validation.confirmed', ['attribute' => 'password']));
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);
                $this->see(trans(PasswordBroker::INVALID_USER), true);
                $this->see(trans(PasswordBroker::INVALID_TOKEN), true);
                $this->see(trans(PasswordBroker::PASSWORD_RESET), true);
                $this->see(trans(PasswordBroker::INVALID_PASSWORD, ['min_length' => \Config::get('auth.password.min_length')]), true);
                break;
            case 'testPasswordReset with data set #2':
                $this->seePageIs($_uri);
                $this->see('<h2><b>Reset</b> your password</h2>');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);
                $this->see(trans(PasswordBroker::INVALID_USER));
                $this->see(trans(PasswordBroker::INVALID_TOKEN), true);
                $this->see(trans(PasswordBroker::PASSWORD_RESET), true);
                $this->see(trans(PasswordBroker::INVALID_PASSWORD, ['min_length' => \Config::get('auth.password.min_length')]), true);
                break;
            case 'testPasswordReset with data set #3':
                $this->seePageIs($_uri);
                $this->see('<h2><b>Reset</b> your password</h2>');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);
                $this->see(trans(PasswordBroker::INVALID_USER), true);
                $this->see(trans(PasswordBroker::INVALID_TOKEN));
                $this->see(trans(PasswordBroker::PASSWORD_RESET), true);
                $this->see(trans(PasswordBroker::INVALID_PASSWORD, ['min_length' => \Config::get('auth.password.min_length')]), true);
                break;
            case 'testPasswordReset with data set #4':
                $this->seePageIs($_uri);
                $this->see('<h2><b>Reset</b> your password</h2>');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]));
                $this->see(trans(PasswordBroker::INVALID_USER), true);
                $this->see(trans(PasswordBroker::INVALID_TOKEN), true);
                $this->see(trans(PasswordBroker::PASSWORD_RESET), true);
                $this->see(trans(PasswordBroker::INVALID_PASSWORD, ['min_length' => \Config::get('auth.password.min_length')]), true);
                break;
            default:
                $this->seePageIs('/login');
                $this->see('<h2><b>Log in</b> to your account</h2>');
                $this->see(trans('validation.required', ['attribute' => 'email']), true);
                $this->see(trans('validation.email', ['attribute' => 'email']), true);
                $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
                $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => \Config::get('auth.password.min_length')]), true);
                $this->see(trans(PasswordBroker::INVALID_USER), true);
                $this->see(trans(PasswordBroker::INVALID_TOKEN), true);
                $this->see(trans(PasswordBroker::PASSWORD_RESET));
                $this->see(trans(PasswordBroker::INVALID_PASSWORD, ['min_length' => \Config::get('auth.password.min_length')]), true);
                break;
        }
    }
}

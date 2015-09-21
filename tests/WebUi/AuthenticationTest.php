<?php namespace App\Tests\WebUi;

use App\Tests\IlluminateTestCase;
use Illuminate\Contracts\Auth\PasswordBroker;

class AuthenticationTest extends IlluminateTestCase
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
        $this->click('Log in');
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

    /**
     * Tests /register route.
     */
    public function testRegisterForFailures()
    {
        # Visit the page, make sure we have landed on right page.
        $this->visit('/register');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Create</b> a new account</h2>');

        # We shouldn't have flash error messages around.
        $this->see(trans('validation.required', ['attribute' => 'name']), true);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);

        # Fill in the form - case 1: Password confirmation missing
        $credentials = ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver'];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Sign up');
        $this->seePageIs('/register');
        $this->see('The password confirmation does not match.');
        $this->see(trans('validation.required', ['attribute' => 'name']), true);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->notSeeInDatabase('users', ['email' => $credentials['email']]);

        # Case 2: Email missing
        $this->visit('/register');
        $credentials = ['name' => 'John Doe', 'password' => 'theWeakestPasswordEver', 'password_confirmation' => 'theWeakestPasswordEver'];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Sign up');
        $this->seePageIs('/register');
        $this->see(trans('validation.required', ['attribute' => 'email']));
        $this->see(trans('validation.required', ['attribute' => 'name']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->notSeeInDatabase('users', ['name' => $credentials['name']]);

        # Case 3: Short password
        $this->visit('/register');
        $credentials = ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'password' => 'short', 'password_confirmation' => 'short'];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Sign up');
        $this->seePageIs('/register');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'name']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]));
        $this->notSeeInDatabase('users', ['email' => $credentials['email']]);
    }

    /**
     * Tests /register route.
     */
    public function testRegisterForSuccess()
    {
        # Visit the page, make sure we have landed on right page.
        $this->visit('/register');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Create</b> a new account</h2>');

        # We shouldn't have flash error messages around.
        $this->see(trans('validation.required', ['attribute' => 'name']), true);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);

        # Fill in the form
        $credentials = ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd', 'password_confirmation' => 's0m34ardPa55w0rd'];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Sign up');
        $this->seePageIs('');
        $this->see('<b>Welcome</b>!');
        $this->seeInDatabase('users', ['email' => $credentials['email']]);
    }

    /**
     * Tests /login route.
     */
    public function testLoginForFailures()
    {
        # Visit the page, make sure we have landed on right page.
        $this->visit('/login');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Log in</b> to your account</h2>');

        # We shouldn't have flash error messages around.
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);

        # Case 1: Missing email
        $credentials = ['password' => 's0m34ardPa55w0rd'];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Log in');
        $this->seePageIs('/login');
        $this->see(trans('validation.required', ['attribute' => 'email']));
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see('These credentials do not match our records!', true);

        # Case 2: Missing password
        $credentials = ['email' => 'john.doe@example.com'];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Log in');
        $this->seePageIs('/login');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']));
        $this->see('These credentials do not match our records!', true);

        # Case 3: Invalid login
        $credentials = ['email' => 'john.doe@example.com', 'password' => 'theWeakestPasswordEver'];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Log in');
        $this->seePageIs('/login');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see('These credentials do not match our records!');
    }

    /**
     * Tests /login route.
     *
     * @depends testRegisterForSuccess
     */
    public function testLoginForSuccess()
    {
        $this->visit('/login');
        $credentials = ['email' => 'john.doe@example.com', 'password' => 's0m34ardPa55w0rd'];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Log in');
        $this->seePageIs('');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.required', ['attribute' => 'password']), true);
        $this->see('These credentials do not match our records!', true);
        $this->see('<b>Welcome</b>, John Doe!');
    }

    /**
     * Tests /logout route.
     */
    public function testLogout()
    {
        app('sentinel')->login(app('sentinel')->getUserRepository()->findById(1));
        $this->visit('/login');
        $this->seeStatusCode(200);
        $this->seePageIs(''); // Since we are authenticated, we are redirected to /
        $this->see('<h2><b>Welcome</b>, John Doe!</h2>');

        $this->click('Log out');
        $this->seeStatusCode(200);
        $this->seePageIs('');
        $this->see('<h2><b>Welcome</b>!</h2>');
    }

    /**
     * Tests /password/email route.
     */
    public function testPasswordEmailForFailures()
    {
        # Visit the page, make sure we have landed on right page.
        $this->visit('/password/email');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Reset</b> your password</h2>');

        # We shouldn't have flash error messages around.
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans(PasswordBroker::RESET_LINK_SENT), true);

        # Case 1: Missing email
        $this->type('', 'email');
        $this->press('Send Password Reset Link');
        $this->seePageIs('/password/email');
        $this->see(trans('validation.required', ['attribute' => 'email']));
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans(PasswordBroker::RESET_LINK_SENT), true);

        # Case 2: Invalid email
        $this->type('john.doe@', 'email');
        $this->press('Send Password Reset Link');
        $this->seePageIs('/password/email');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']));
        $this->see(trans('passwords.user'), true);
        $this->see(trans(PasswordBroker::RESET_LINK_SENT), true);

        # Case 3: Non-existent email
        $this->type('jane.doe@example.com', 'email');
        $this->press('Send Password Reset Link');
        $this->seePageIs('/password/email');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('passwords.user'));
        $this->see(trans(PasswordBroker::RESET_LINK_SENT), true);
    }

    /**
     * Tests /password/email route.
     *
     * @depends testRegisterForSuccess
     */
    public function testPasswordEmailForSuccess()
    {
        # Visit the page, make sure we have landed on right page.
        $this->visit('/password/email');
        $this->type('john.doe@example.com', 'email');
        $this->press('Send Password Reset Link');
        $this->seePageIs('/password/email');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans(PasswordBroker::RESET_LINK_SENT));
    }

    /**
     * Tests /password/reset route.
     */
    public function testPasswordResetForFailuresWhereTokenSegmentInRequestUriIsMissing()
    {
        $this->visit('/password/reset');
        $this->seePageIs('/password/reset');
        $this->seeStatusCode(200);
        $this->see('<h2><b>Reset</b> your password</h2>');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'));
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
    }

    /**
     * Tests /password/reset/{token?} route.
     *
     * @depends testPasswordEmailForSuccess
     */
    public function testPasswordResetForFailuresWherePasswordConfirmationMismatches()
    {
        $passwordResetToken = app('db')->table('reminders')->where('user_id', '=', 1)->value('code');

        $this->visit('/password/reset/' . $passwordResetToken);
        $this->seeStatusCode(200);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
        $credentials = [
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rd'
        ];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Reset Password');
        $this->seePageIs('/password/reset/' . $passwordResetToken);
        $this->see('<h2><b>Reset</b> your password</h2>');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']));
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
    }

    /**
     * Tests /password/reset/{token?} route.
     *
     * @depends testPasswordEmailForSuccess
     */
    public function testPasswordResetForFailuresWherePasswordConfirmationIsMissing()
    {
        $passwordResetToken = app('db')->table('reminders')->where('user_id', '=', 1)->value('code');

        $this->visit('/password/reset/' . $passwordResetToken);
        $this->seeStatusCode(200);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
        $credentials = [
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0'
        ];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Reset Password');
        $this->seePageIs('/password/reset/' . $passwordResetToken);
        $this->see('<h2><b>Reset</b> your password</h2>');
        $this->see('The email field is required.', true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']));
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
    }

    /**
     * Tests /password/reset/{token?} route.
     *
     * @depends testPasswordEmailForSuccess
     */
    public function testPasswordResetForFailuresWhereWrongAccountIsSupplied()
    {
        $passwordResetToken = app('db')->table('reminders')->where('user_id', '=', 1)->value('code');

        $this->visit('/password/reset/' . $passwordResetToken);
        $this->seeStatusCode(200);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
        $credentials = [
            'email' => 'jane.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0'
        ];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Reset Password');
        $this->seePageIs('/password/reset/' . $passwordResetToken);
        $this->see('<h2><b>Reset</b> your password</h2>');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('passwords.user'));
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
    }

    /**
     * Tests /password/reset/{token?} route.
     *
     * @depends testPasswordEmailForSuccess
     */
    public function testPasswordResetForFailuresWhereWrongTokenIsSupplied()
    {
        $this->visit('/password/reset/wrong-token');
        $this->seeStatusCode(200);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
        $credentials = [
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0'
        ];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Reset Password');
        $this->seePageIs('/password/reset/wrong-token');
        $this->see('<h2><b>Reset</b> your password</h2>');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'));
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
    }

    /**
     * Tests /password/reset/{token?} route.
     *
     * @depends testPasswordEmailForSuccess
     */
    public function testPasswordResetForFailuresWhereShortPasswordIsSupplied()
    {
        $passwordResetToken = app('db')->table('reminders')->where('user_id', '=', 1)->value('code');

        $this->visit('/password/reset/' . $passwordResetToken);
        $this->seeStatusCode(200);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
        $credentials = [
            'email' => 'john.doe@example.com',
            'password' => 'short',
            'password_confirmation' => 'short'
        ];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Reset Password');
        $this->seePageIs('/password/reset/' . $passwordResetToken);
        $this->see('<h2><b>Reset</b> your password</h2>');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]));
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
    }

    /**
     * Tests /password/reset/{token?} route.
     *
     * @depends testPasswordEmailForSuccess
     */
    public function testPasswordResetForSuccess()
    {
        $passwordResetToken = app('db')->table('reminders')->where('user_id', '=', 1)->value('code');
        $this->visit('/password/reset/' . $passwordResetToken);
        $this->seeStatusCode(200);
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'), true);
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
        $credentials = [
            'email' => 'john.doe@example.com',
            'password' => 's0m34ardPa55w0rdV3r510nTw0',
            'password_confirmation' => 's0m34ardPa55w0rdV3r510nTw0'
        ];
        foreach ($credentials as $key => $value) {
            $this->type($value, $key);
        }
        $this->press('Reset Password');
        $this->seePageIs('/login');
        $this->see('<h2><b>Log in</b> to your account</h2>');
        $this->see(trans('validation.required', ['attribute' => 'email']), true);
        $this->see(trans('validation.email', ['attribute' => 'email']), true);
        $this->see(trans('validation.confirmed', ['attribute' => 'password']), true);
        $this->see(trans('validation.min.string', ['attribute' => 'password', 'min' => config('auth.password.min_length')]), true);
        $this->see(trans('passwords.user'), true);
        $this->see(trans('passwords.token'), true);
        $this->see(trans('passwords.reset'));
        $this->see(trans('passwords.password', ['min_length' => config('auth.password.min_length')]), true);
    }
}

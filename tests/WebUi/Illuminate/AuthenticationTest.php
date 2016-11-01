<?php namespace App\Tests\WebUi\Illuminate;

use App\Tests\IlluminateTestCase;
use Illuminate\Contracts\Console\Kernel;

class AuthenticationTest extends IlluminateTestCase
{
    protected static $startUrl = 'http://basis.audith.org';

    public static $browsers = [
        [
            'browserName' => 'firefox',
            'desiredCapabilities' => [
                'version' => '46',
                'platform' => 'Windows 8.1'
            ]
        ]
    ];

    public static function setUpBeforeClass()
    {
        putenv('APP_ENV=testing');

        // Create an app instance for facades to work
        $app = require __DIR__ . '/../../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        // Re-Migrate
        \Artisan::call('migrate:refresh');
    }

    public function testAuthenticateMiddleware()
    {
        $this->visit('/oauth/clients');
        $this->seePageIs('/en/login');
        $this->seeRouteIs('en.login');
    }

    public function testHome()
    {
        $this->visit('/');
        $this->seePageIs('/en');
        $this->seeRouteIs('en.home');
    }
}

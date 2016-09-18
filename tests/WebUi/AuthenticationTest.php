<?php namespace App\Tests\WebUi;

use App\Tests\SauceWebDriverTestCase;
use Illuminate\Contracts\Console\Kernel;

class AuthenticationTest extends SauceWebDriverTestCase
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
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        // Re-Migrate
        \Artisan::call('migrate:refresh');
    }

    public function setUp()
    {
        $this->setBrowserUrl('');
    }

    public function testAuthenticateMiddleware()
    {
        $this->url(self::$startUrl . '/oauth/clients'); // An 'auth'-enabled path.
        $this->assertEquals('http://basis.audith.org/en/login', $this->url()); // We get redirected.
        $this->assertContains('Login - Audith Basis', $this->title());
    }

    public function testRegister()
    {
        $this->url(self::$startUrl); // Trying to hit root path '/'
        $this->assertEquals(self::$startUrl . '/en', $this->url()); // We get redirected to '/en' localized path.
        $this->assertContains('Audith Basis', $this->title());
    }
}

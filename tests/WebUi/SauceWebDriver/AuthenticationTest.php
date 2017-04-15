<?php namespace App\Tests\WebUi\SauceWebDriver;

use App\Tests\SauceWebDriverTestCase;
use Illuminate\Contracts\Console\Kernel;

class AuthenticationTest extends SauceWebDriverTestCase
{
    /** @var bool */
    public static $migrated = false;

    /** @var string */
    protected static $startUrl = 'http://basis.audith.org';

    /** @var array */
    public static $browsers = [
        [
            'browserName' => 'firefox',
            'desiredCapabilities' => [
                'version' => '46',
                'platform' => 'Windows 8.1'
            ]
        ]
    ];

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

    public function testHome()
    {
        $this->url(self::$startUrl); // Trying to hit root path '/'
        $this->assertEquals(self::$startUrl . '/en', $this->url()); // We get redirected to '/en' localized path.
        $this->assertContains('Audith Basis', $this->title());
    }
}

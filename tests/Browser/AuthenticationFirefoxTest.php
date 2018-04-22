<?php namespace App\Tests\Browser;

use App\Tests\DuskFirefoxTestCase;
use Laravel\Dusk\Browser;

class AuthenticationFirefoxTest extends DuskFirefoxTestCase
{
    public function setUp()
    {
        // Migrations should run only once, before application is created (the moment when $this->app == null).
        if (is_null($this->app)) {
            $this->afterApplicationCreated(function () {
                $this->artisan('migrate:reset');
                $this->artisan('migrate');
            });
        }

        parent::setUp();
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function testAuthenticateMiddleware()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/oauth/clients');
//            $browser->waitForText('Login');
            $browser->assertPathIs('/en/login');
        });
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function testHome()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
            $browser->assertPathIs('/en');
        });
    }
}

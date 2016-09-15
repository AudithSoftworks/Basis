<?php namespace App\Tests;

class SauceWebDriverTestCase extends \Sauce\Sausage\WebDriverTestCase
{
    public static $sauceHost;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}

<?php

use Illuminate\Foundation\Testing\ApplicationTrait;
use Illuminate\Foundation\Testing\CrawlerTrait;

class SauceWebDriverTestCase extends Sauce\Sausage\WebDriverTestCase
{
    use ApplicationTrait;

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

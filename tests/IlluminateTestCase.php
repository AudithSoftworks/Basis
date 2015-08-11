<?php namespace App\Tests;

use Illuminate\Foundation\Testing\TestCase;

class IlluminateTestCase extends TestCase
{
    protected $baseUrl = 'http://basis.audith.org';

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

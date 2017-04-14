<?php namespace App\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class IlluminateTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUrl = 'http://basis.audith.org';
}

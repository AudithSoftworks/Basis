<?php namespace App\Tests;

class SauceWebDriverTestCase extends \Sauce\Sausage\WebDriverTestCase
{
    use CreatesApplication;

    public static $sauceHost;
}

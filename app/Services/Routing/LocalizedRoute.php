<?php namespace App\Services\Routing;

use Illuminate\Routing\Route;

class LocalizedRoute extends Route
{
    /**
     * {@inheritdoc}
     */
    public function __construct($methods, $uri, $action)
    {
        parent::__construct($methods, $uri, $action);
        $this->locale = app('translator')->getLocale();
    }
}
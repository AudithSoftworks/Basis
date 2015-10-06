<?php namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->patterns(
            ['provider' => 'twitter|google|facebook'],
            ['token' => '[a-zA-Z0-9]+']
        );

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function map(Router $router)
    {
        //-----------------------------------------------------------------------
        // Non-localized, generic routes (such as those for admin panel etc).
        //-----------------------------------------------------------------------

        $router->group(['namespace' => $this->namespace], function (Router $router) {
            $router->get('/php-info', function () {
                ob_start();
                phpinfo();

                return ob_get_clean();
            });

            $router->get('/oauth/{provider}', 'Users\AuthController@getOAuth');

            $router->controllers([
                '/admin' => 'AdminController',
                '/admin-demo' => 'Admin\DemoController'
            ]);
        });

        //-----------------------------------------------------------------------------------------------------
        // Register localized routes with locale-prefices (in case of default locale, no prefix is attached).
        //-----------------------------------------------------------------------------------------------------

        $namespace = $this->namespace;
        $middleware = 'locale';
        foreach (\Config::get('app.locales') as $prefix => $localeName) {
            \Lang::setLocale($prefix);
            // Skip default locale for now.
            if ($prefix === \Config::get('app.locale')) {
                continue;
            }

            // Set localized routers.
            $router->group(compact('namespace', 'middleware', 'prefix'), function (Router $router) use ($prefix) {
                $this->localizedRoutes($router, $prefix);
            });
        }

        //------------------------------------------------
        // Default locale? No prefices are necessary.
        //------------------------------------------------

        $defaultLocale = config('app.locale');
        app('translator')->setLocale($defaultLocale);
        $router->group(compact('namespace'), function (Router $router) use ($defaultLocale) {
            $this->localizedRoutes($router, $defaultLocale);
        });
    }

    protected function localizedRoutes(Router $router, $prefix)
    {
        $router->get('login/{provider?}', ['uses' => 'Users\AuthController@getLogin', 'as' => $prefix . '.login']);
        $router->post('login/{provider?}', 'Users\AuthController@postLogin');
        $router->get('logout', ['uses' => 'Users\AuthController@getLogout', 'as' => $prefix . '.logout']);
        $router->get('register', ['uses' => 'UsersController@create', 'as' => $prefix . '.register']);
        $router->post('register', 'UsersController@store');

        $router->resource('users', 'UsersController');
        $router->controller('password', 'Users\PasswordController');
        $router->controller('activation', 'Users\ActivationController');

        $router->resource('files', 'FilesController');

        $router->controller('/', 'HomeController');
    }
}

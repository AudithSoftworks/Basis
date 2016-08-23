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
        $router->patterns([
            'provider' => 'twitter|google|facebook',
            'token' => '[a-zA-Z0-9-]+'
        ]);

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function map(Router $router)
    {
        $defaultLocale = config('app.locale');
        $namespace = $this->namespace;
        $middleware = 'web';

        //-----------------------------------------------------------------------
        // Non-localized, generic routes (such as those for admin panel etc).
        //-----------------------------------------------------------------------

        $router->group(compact('namespace', 'middleware'), function (Router $router) {
            $router->get('/oauth/{provider}', 'Users\AuthController@getOAuth');
        });

        //-----------------------------------------------------------------------------------------------------
        // Register localized routes with locale-prefices (in case of default locale, no prefix is attached).
        //-----------------------------------------------------------------------------------------------------

        foreach (config('app.locales') as $prefix => $localeName) {
            app('translator')->setLocale($prefix);
            // Skip default locale for now.
            if ($prefix === $defaultLocale) {
                continue;
            }

            // Set localized routers.
            $router->group(compact('namespace', 'middleware', 'prefix'), function (Router $router) use ($prefix) {
                $this->localizedRoutes($router, $prefix);
            });
        }

        //------------------------------------------------
        // Default locale: No prefices are necessary.
        //------------------------------------------------

        app('translator')->setLocale($defaultLocale);
        $router->group(compact('namespace', 'middleware'), function (Router $router) use ($defaultLocale) {
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

        $router->get('password/email', 'Users\PasswordController@requestPasswordResetLink');
        $router->post('password/email', 'Users\PasswordController@sendPasswordResetLink');
        $router->get('password/reset/{token}', 'Users\PasswordController@showPasswordResetForm');
        $router->post('password/reset', 'Users\PasswordController@resetPassword');

        $router->get('activation', 'Users\ActivationController@requestActivationCode');
        $router->get('activation/{token}', 'Users\ActivationController@activate');
        $router->post('activation', 'Users\ActivationController@activate');

        $router->resource('files', 'FilesController', ['only' => ['index', 'create', 'store', 'show', 'destroy']]);

        $router->get('', 'HomeController@index');

        $router->get('admin', 'AdminController@index');
    }
}

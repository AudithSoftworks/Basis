<?php namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        app('router')->patterns([
            'provider' => 'twitter|google|facebook',
            'token' => '[a-zA-Z0-9-]+'
        ]);

        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "api" routes for the application.
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        app('router')->group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api/v1',
        ], function (Router $router) {
            $router->post('login', 'Auth\LoginController@login');
            $router->post('logout', 'Auth\LoginController@logout')->middleware('auth:api');
            $router->post('register', 'Auth\RegisterController@register');

            $router->post('password/email', 'Auth\PasswordController@sendPasswordResetLink');
            $router->post('password/reset', 'Auth\PasswordController@resetPassword');

            $router->get('activation', 'Auth\ActivateController@requestActivationCode')->middleware('auth:api');
            $router->get('activation/{token}', 'Auth\ActivateController@activate')->middleware('auth:api');
            $router->post('activation', 'Auth\ActivateController@activate')->middleware('auth:api');
        });
    }

    /**
     * Define the "web" routes for the application.
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        app('router')->group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function (Router $router) {
            $defaultLocale = config('app.locale');

            //-----------------------------------------------------------------------
            // Non-localized, generic routes (such as those for admin panel etc).
            //-----------------------------------------------------------------------

            $router->get('/oauth/to/{provider}', ['uses' => 'Auth\LoginController@handleOAuthRedirect', 'as' => 'oauth.to']);
            $router->get('/oauth/from/{provider}', ['uses' => 'Auth\LoginController@handleOAuthReturn', 'as' => 'oauth.from']);

            //-----------------------------------------------------------------------------------------------------
            // Register localized routes with locale-prefices (in case of default locale, no prefix is attached).
            //-----------------------------------------------------------------------------------------------------

            foreach (config('app.locales') as $prefix => $localeName) {
                app('translator')->setLocale($prefix);
                // Localized routes.
                $router->group(compact('namespace', 'middleware', 'prefix'), function (Router $router) use ($prefix) {
                    $this->localizeWebRoutes($router, $prefix);
                });
            }

            /*------------------------------------
             | Default, non-localized home
             *-----------------------------------*/

            $router->get('', 'HomeController@index');
        });
    }

    /**
     * @param \Illuminate\Routing\Router $router
     * @param string                     $prefix
     */
    protected function localizeWebRoutes(Router $router, $prefix = '')
    {
        $router->get('login', ['uses' => 'Auth\LoginController@showLoginForm', 'as' => empty($prefix) ?: $prefix . '.login']);
        $router->post('login', 'Auth\LoginController@loginViaWeb');
        $router->get('logout', ['uses' => 'Auth\LoginController@logout', 'as' => empty($prefix) ?: $prefix . '.logout']);
        $router->get('register', ['uses' => 'Auth\RegisterController@showRegistrationForm', 'as' => empty($prefix) ?: $prefix . '.register']);
        $router->post('register', 'Auth\RegisterController@register');

        $router->get('password/email', ['uses' => 'Auth\PasswordController@requestPasswordResetLink', 'as' => empty($prefix) ?: $prefix . '.password.email']);
        $router->post('password/email', 'Auth\PasswordController@sendPasswordResetLink');
        $router->get('password/reset/{token}', ['uses' => 'Auth\PasswordController@showPasswordResetForm', 'as' => empty($prefix) ?: $prefix . '.password.reset']);
        $router->post('password/reset', 'Auth\PasswordController@resetPassword');

        $router->get('activation', ['uses' => 'Auth\ActivateController@requestActivationCode', 'as' => empty($prefix) ?: $prefix . '.activation.request']);
        $router->get('activation/{token}', ['uses' => 'Auth\ActivateController@activate', 'as' => empty($prefix) ?: $prefix . '.activation.complete']);
        $router->post('activation', 'Auth\ActivateController@activate');

        $router->resource('files', 'FilesController', ['only' => ['index', 'create', 'store', 'show', 'destroy']]);

        $router->get('', ['uses' => 'HomeController@index', 'as' => empty($prefix) ?: $prefix . '.home']);
    }
}

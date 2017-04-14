<?php namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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
        app('router')->prefix('api/v1')->middleware('api')->namespace($this->namespace)->group(base_path('routes/api.php'));
    }

    /**
     * Define the "web" routes for the application.
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        app('router')->middleware('web')->namespace($this->namespace)->group(base_path('routes/web.php'));
    }
}

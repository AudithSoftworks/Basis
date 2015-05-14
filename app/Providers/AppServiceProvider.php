<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\File;
use App\Services\Assetic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Contracts\Registrar', 'App\Services\Registrar');

        $this->app->singleton('App\Contracts\File', function ($app) {
            return new File($app['config']['file']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'App\Contracts\Registrar',
            'App\Contracts\File'
        ];
    }
}

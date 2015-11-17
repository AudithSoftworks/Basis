<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RegistrarServiceProvider extends ServiceProvider
{
    /**
     * Deferring the loading of such a provider will improve the performance of your application,
     * since it is not loaded from the filesystem on every request.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Contracts\Registrar::class, \App\Services\Registrar::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [\App\Contracts\Registrar::class];
    }
}

<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('App\Services\File', function($app){
            return new \App\Services\File($app['config']['file']);
        });
	}

}

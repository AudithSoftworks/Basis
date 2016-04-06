<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Overwrite any vendor / package configuration.
     *
     * This service provider is intended to provide a convenient location for you
     * to overwrite any "vendor" or package configuration that you may want to
     * modify before the application handles the incoming request / command.
     *
     * @return void
     */
    public function register()
    {
        # Adding Hipchat and Gitter handlers to Monolog logger for non-production environments.
        if (!app()->environment('production')) {
            /*
            |--------------------------
            | Hipchat integration
            |--------------------------
            */
            $hipchatConfig = app('config')->get('services.hipchat');
            $hipchatHandler = new \Monolog\Handler\HipChatHandler(
                $hipchatConfig['token'],
                $hipchatConfig['room'],
                $hipchatConfig['name'],
                false,
                $hipchatConfig['level']
            );
            $bufferHandlerForHipchat = new \Monolog\Handler\BufferHandler($hipchatHandler);
            app('log')->getMonolog()->pushHandler($bufferHandlerForHipchat);
        }

        config([
            //
        ]);
    }
}

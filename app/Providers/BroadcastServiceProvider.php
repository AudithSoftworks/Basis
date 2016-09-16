<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
//use Illuminate\Support\Facades\Broadcast;
use Illuminate\Broadcasting\BroadcastManager;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app(BroadcastManager::class)->routes();

        // Authenticate the user's personal channel...
        app(BroadcastManager::class)->channel('App.Models.User.*', function ($user, $userId) {
            return (int)$user->id === (int)$userId;
        });
    }
}

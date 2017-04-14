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

        /*--------------------------------------------------------------------------------
         | Here you may register all of the event broadcasting channels that your
         | application supports. The given channel authorization callbacks are
         | used to check if an authenticated user can listen to the channel.
         *-------------------------------------------------------------------------------*/

        // Authenticate the user's personal channel...
        app(BroadcastManager::class)->channel('App.Models.User.*', function ($user, $userId) {
            return (int)$user->id === (int)$userId;
        });
    }
}

<?php namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\Users\Deleted::class => [],
        \App\Events\Users\LoggedIn::class => [],
        \App\Events\Users\LoggedOut::class => [],
        \App\Events\Users\Registered::class => [
            \App\Listeners\Users\SendActivationLinkViaEmail::class
        ],
        \App\Events\Users\RequestedActivationLink::class => [
            \App\Listeners\Users\SendActivationLinkViaEmail::class
        ],
        \App\Events\Users\RequestedResetPasswordLink::class => [
            \App\Listeners\Users\SendResetPasswordLinkViaEmail::class
        ],
        \App\Events\Users\Updated::class => [],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}

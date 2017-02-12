<?php namespace App\Providers;

use App\Events as Events;
use App\Listeners as Listeners;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Events\Files\Uploaded::class => [
            Listeners\Files\ValidateUploadRealMimeAgainstAllowedTypes::class,
            Listeners\Files\PersistUploadedFile::class
        ],
        Events\Users\LoggedIn::class => [],
        Events\Users\LoggedOut::class => [],
        Events\Users\Registered::class => [
            Listeners\Users\SendActivationLinkViaEmail::class
        ],
        Events\Users\RequestedActivationLink::class => [
            Listeners\Users\SendActivationLinkViaEmail::class
        ],
        Events\Users\RequestedResetPasswordLink::class => [],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

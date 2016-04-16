<?php namespace App\Listeners\Users;

use App\Traits\Users\Activates;
use Illuminate\Mail\Message;

class SendActivationLinkViaEmail
{
    use Activates;

    public function handle($event) // Don't type-hint: this listener can be triggered by different events.
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        if (false === ($activation = $this->exists($user))) {
            $activation = $this->create($user);
        }
        $token = $activation->code;
        $view = config('auth.activations.view');
        app('mailer')->send($view, compact('user', 'token'), function (Message $m) use ($user, $token) {
            $m->to($user->email)->subject($this->getActivationEmailSubject());
        });
    }
}

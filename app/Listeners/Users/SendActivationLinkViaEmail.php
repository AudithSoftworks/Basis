<?php namespace App\Listeners\Users;

use Illuminate\Mail\Message;

class SendActivationLinkViaEmail
{
    public function handle($event) // Don't type-hint: this listener can be triggered by different events.
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        if (false === ($activation = app('sentinel.activations')->exists($user))) {
            $activation = app('sentinel.activations')->create($event->user);
        }
        $token = $activation->code;
        $view = config('cartalyst.sentinel.activations.view');
        app('mailer')->send($view, compact('user', 'token'), function (Message $m) use ($user, $token) {
            $m->to($user->email)->subject($this->getActivationEmailSubject());
        });
    }

    /**
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    private function getActivationEmailSubject()
    {
        return trans('auth.activation_email_subject');
    }
}

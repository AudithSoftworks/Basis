<?php namespace App\Listeners\Users;

use App\Events\Users\RequestedResetPasswordLink;
use Illuminate\Mail\Message;

class SendResetPasswordLinkViaEmail
{
    public function handle(RequestedResetPasswordLink $event)
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        $reminder = app('sentinel.reminders')->create($event->user);
        $token = $reminder->code;
        $view = config('auth.password.email');
        app('mailer')->send($view, compact('user', 'token'), function (Message $m) use ($user, $token) {
            $m->to($user->email)->subject($this->getPasswordResetEmailSubject());
        });
    }

    /**
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    private function getPasswordResetEmailSubject()
    {
        return trans('passwords.reset_email_subject');
    }
}

<?php namespace App\Listeners\Users;

use App\Events\Users\RequestedResetPasswordLink;
use Illuminate\Mail\Message;

class SendResetPasswordLinkViaEmail
{
    public function handle(RequestedResetPasswordLink $event)
    {
        /** @var \App\Models\User $user */
        $user = $event->user;

        $broker = isset($this->broker) ? $this->broker : null;
        app('auth.password')->broker($broker)->sendResetLink(
            ['email' => $user->email], function (Message $message) {
                $message->subject($this->getPasswordResetEmailSubject());
            }
        );
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

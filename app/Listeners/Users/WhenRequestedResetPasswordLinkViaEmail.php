<?php namespace App\Listeners\Users;

use App\Events\Users\RequestedResetPasswordLinkViaEmail;
use Illuminate\Mail\Message;

class WhenRequestedResetPasswordLinkViaEmail
{
    public function handle(RequestedResetPasswordLinkViaEmail $event)
    {
        /** @var \Illuminate\Auth\Passwords\TokenRepositoryInterface $tokenRepository */
        $tokenRepository = \App::make('Illuminate\Auth\Passwords\TokenRepositoryInterface');
        $token = $tokenRepository->create($event->user);
        /** @var \Illuminate\Auth\Passwords\PasswordBroker $passwordBroker */
        $passwordBroker = \App::make('Illuminate\Contracts\Auth\PasswordBroker');
        $passwordBroker->emailResetLink($event->user, $token, function (Message $message) {
            $message->subject($this->getPasswordResetEmailSubject());
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

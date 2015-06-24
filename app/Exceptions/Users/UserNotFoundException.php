<?php namespace App\Exceptions\Users;

use Illuminate\Contracts\Auth\PasswordBroker;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(trans(PasswordBroker::INVALID_USER), $previous, $code);
    }
}

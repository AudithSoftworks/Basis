<?php namespace App\Exceptions\Users;

use Illuminate\Contracts\Auth\PasswordBroker;

class TokenNotValidException extends \UnexpectedValueException
{
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(trans(PasswordBroker::INVALID_TOKEN), $code, $previous);
    }
}

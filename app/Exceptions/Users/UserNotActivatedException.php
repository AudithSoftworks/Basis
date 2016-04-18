<?php namespace App\Exceptions\Users;

use Illuminate\Auth\Access\AuthorizationException;

class UserNotActivatedException extends AuthorizationException
{
}

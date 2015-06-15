<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Illuminate\Contracts\Auth\CanResetPassword;

class ResetPassword extends EventAbstract
{
    /**
     * @var CanResetPassword
     */
    public $user;

    public function __construct(CanResetPassword $user)
    {
        $this->user = $user;

        parent::__construct();
    }
}

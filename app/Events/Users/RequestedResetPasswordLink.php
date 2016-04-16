<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Illuminate\Contracts\Auth\Authenticatable;

class RequestedResetPasswordLink extends EventAbstract
{
    /**
     * @var array|\Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;

        parent::__construct();
    }
}

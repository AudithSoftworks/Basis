<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Illuminate\Contracts\Auth\Authenticatable;

class LoggedOut extends EventAbstract
{
    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;

        parent::__construct();
    }
}

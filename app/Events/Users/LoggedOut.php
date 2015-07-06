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
     * @param Authenticatable|null $user
     */
    public function __construct($user)
    {
        $this->user = $user;

        parent::__construct();
    }
}

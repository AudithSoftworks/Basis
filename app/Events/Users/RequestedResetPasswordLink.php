<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Cartalyst\Sentinel\Users\UserInterface;

class RequestedResetPasswordLink extends EventAbstract
{
    /**
     * @var array|\Cartalyst\Sentinel\Users\UserInterface
     */
    public $user;

    /**
     * @param \Cartalyst\Sentinel\Users\UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;

        parent::__construct();
    }
}

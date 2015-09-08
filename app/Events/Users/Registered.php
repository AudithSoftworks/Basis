<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Cartalyst\Sentinel\Users\UserInterface;

class Registered extends EventAbstract
{
    /**
     * @var \Cartalyst\Sentinel\Users\UserInterface
     */
    public $user;

    /**
     * @var null|string
     */
    public $oauthProviderName;

    /**
     * @param \Cartalyst\Sentinel\Users\UserInterface $user
     * @param string|null                             $oauthProviderName
     */
    public function __construct(UserInterface $user, $oauthProviderName = null)
    {
        $this->user = $user;
        $this->oauthProviderName = $oauthProviderName;

        parent::__construct();
    }
}

<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Cartalyst\Sentinel\Users\UserInterface;

class Updated extends EventAbstract
{
    /**
     * @var \Cartalyst\Sentinel\Users\UserInterface
     */
    public $userBefore;

    /**
     * @var \Cartalyst\Sentinel\Users\UserInterface
     */
    public $userAfter;

    /**
     * @var null|string
     */
    public $oauthProviderName;

    /**
     * @param \Cartalyst\Sentinel\Users\UserInterface $user
     * @param \Cartalyst\Sentinel\Users\UserInterface $userAfter
     * @param string|null                             $oauthProviderName
     */
    public function __construct(UserInterface $user, UserInterface $userAfter, $oauthProviderName = null)
    {
        $this->userBefore = $user;
        $this->userAfter = $userAfter;
        $this->oauthProviderName = $oauthProviderName;

        parent::__construct();
    }
}

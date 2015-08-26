<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Cartalyst\Sentinel\Users\UserInterface;

class LoggedIn extends EventAbstract
{
    /**
     * @var array|\Cartalyst\Sentinel\Users\UserInterface
     */
    public $user;

    /**
     * @var null|string
     */
    public $oauthProviderNameIfApplicable;

    /**
     * @param \Cartalyst\Sentinel\Users\UserInterface $user
     * @param string|null                             $oauthProviderName
     */
    public function __construct(UserInterface $user, $oauthProviderName = null)
    {
        $this->user = $user;
        $this->oauthProviderNameIfApplicable = $oauthProviderName;

        parent::__construct();
    }
}

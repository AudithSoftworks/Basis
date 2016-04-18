<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Illuminate\Contracts\Auth\Authenticatable;

class LoggedIn extends EventAbstract
{
    /**
     * @var array|\Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * @var null|string
     */
    public $oauthProviderNameIfApplicable;

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string|null                                $oauthProviderName
     */
    public function __construct(Authenticatable $user, $oauthProviderName = null)
    {
        $this->user = $user;
        $this->oauthProviderNameIfApplicable = $oauthProviderName;

        parent::__construct();
    }
}

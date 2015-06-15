<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Illuminate\Contracts\Auth\Authenticatable;

class LoggedIn extends EventAbstract
{
    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * @var null|string
     */
    public $oauthProviderNameIfApplicable;

    /**
     * @param Authenticatable $user
     * @param string|null     $oauthProviderNameIfApplicable
     */
    public function __construct(Authenticatable $user, $oauthProviderNameIfApplicable = null)
    {
        $this->user = $user;
        $this->oauthProviderNameIfApplicable = $oauthProviderNameIfApplicable;

        parent::__construct();
    }
}

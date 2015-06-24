<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Illuminate\Contracts\Auth\Authenticatable;

class Updated extends EventAbstract
{
    /**
     * @var Authenticatable
     */
    public $userBefore;

    /**
     * @var Authenticatable
     */
    public $userAfter;

    /**
     * @var null|string
     */
    public $oauthProviderNameIfApplicable;

    /**
     * @param Authenticatable $userBefore
     * @param Authenticatable $userAfter
     * @param string|null     $oauthProviderNameIfApplicable
     */
    public function __construct(Authenticatable $userBefore, Authenticatable $userAfter, $oauthProviderNameIfApplicable = null)
    {
        $this->userBefore = $userBefore;
        $this->userAfter = $userAfter;
        $this->oauthProviderNameIfApplicable = $oauthProviderNameIfApplicable;

        parent::__construct();
    }
}

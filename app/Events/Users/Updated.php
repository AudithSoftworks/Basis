<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Illuminate\Contracts\Auth\Authenticatable;

class Updated extends EventAbstract
{
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $userBefore;

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $userAfter;

    /**
     * @var null|string
     */
    public $oauthProviderName;

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \Illuminate\Contracts\Auth\Authenticatable $userAfter
     * @param string|null                                $oauthProviderName
     */
    public function __construct(Authenticatable $user, Authenticatable $userAfter, $oauthProviderName = null)
    {
        $this->userBefore = $user;
        $this->userAfter = $userAfter;
        $this->oauthProviderName = $oauthProviderName;

        parent::__construct();
    }
}

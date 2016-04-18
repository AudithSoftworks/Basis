<?php namespace App\Events\Users;

use App\Events\Event as EventAbstract;
use Illuminate\Contracts\Auth\Authenticatable;

class Registered extends EventAbstract
{
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * @var null|string
     */
    public $oauthProviderName;

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string|null                                $oauthProviderName
     */
    public function __construct(Authenticatable $user, $oauthProviderName = null)
    {
        $this->user = $user;
        $this->oauthProviderName = $oauthProviderName;

        parent::__construct();
    }
}

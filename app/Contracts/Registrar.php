<?php namespace App\Contracts;

use Illuminate\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Laravel\Socialite\AbstractUser as SocialiteUser;

interface Registrar
{
    /**
     * @param Request        $request
     * @param Guard          $auth
     * @param PasswordBroker $password
     */
    public function __construct(Request $request, Guard $auth, PasswordBroker $password);

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function register();

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     *
     * @return Authenticatable|bool
     */
    public function registerViaOAuth(SocialiteUser $oauthUserData, $provider);

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id);

    /**
     * @param integer $id
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function get($id);

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function update($id);

    /**     *
     * @return boolean
     */
    public function login();

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     *
     * @return bool
     */
    public function loginViaOAuth(SocialiteUser $oauthUserData, $provider);

    /**
     * @return boolean
     */
    public function logout();

    /**
     * @return boolean
     */
    public function sendResetPasswordLinkViaEmail();

    /**
     * @return boolean
     */
    public function resetPassword();
}

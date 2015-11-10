<?php namespace App\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\AbstractUser as SocialiteUser;

interface Registrar
{
    /**
     * @return \Cartalyst\Sentinel\Users\UserInterface
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
     * @param string $token
     *
     * @return bool
     */
    public function activate($token = null);

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id);

    /**
     * @param integer $id
     *
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    public function get($id);

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function update($id);

    /**     *
     * @return bool|\Cartalyst\Sentinel\Users\UserInterface
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

<?php namespace App\Services;

use App\Contracts\Registrar as RegistrarContract;
use App\Events\Users\LoggedIn;
use App\Events\Users\LoggedOut;
use App\Events\Users\Registered;
use App\Events\Users\RequestedResetPasswordLink;
use App\Events\Users\ResetPassword;
use App\Events\Users\Updated;
use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\LoginNotValidException;
use App\Exceptions\Users\PasswordNotValidException;
use App\Exceptions\Users\TokenNotValidException;
use App\Models\User;
use App\Models\UserActivation;
use App\Models\UserOAuth;
use App\Traits\Users\Activates;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Laravel\Socialite\AbstractUser as SocialiteUser;

class Registrar implements RegistrarContract
{
    use AuthenticatesAndRegistersUsers, Activates, ThrottlesLogins, ValidatesRequests;

    /** @var \Illuminate\Http\Request */
    protected $request;

    public function __construct()
    {
        $this->request = app('router')->getCurrentRequest();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     * @throws \App\Exceptions\Common\ValidationException
     */
    public function register()
    {
        $validator = app('validator')->make($this->request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:' . config('auth.passwords.users.min_length'),
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = new User();
        $this->request->has('name') && $user->name = $this->request->input('name');
        $user->email = $this->request->input('email');
        $user->password = app('hash')->make($this->request->input('password'));
        $user->save() && event(new Registered($user));

        return $user;
    }

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|bool
     */
    public function registerViaOAuth(SocialiteUser $oauthUserData, $provider)
    {
        /** @var \App\Models\User $ownerAccount */
        if (!($ownerAccount = User::withTrashed()->whereEmail($oauthUserData->email)->first())) {
            $ownerAccount = User::create([
                'name' => $oauthUserData->name,
                'email' => $oauthUserData->email,
                'password' => app('hash')->make(uniqid("", true))
            ]);
            event(new Registered($ownerAccount, $provider));
        }

        # If user account is soft-deleted, restore it.
        $ownerAccount->trashed() && $ownerAccount->restore();

        # Update missing user name.
        if (!$ownerAccount->name) {
            $ownerAccount->name = $oauthUserData->name;
            $ownerAccount->save();
        }

        ($doLinkOAuthAccount = $this->linkOAuthAccount($oauthUserData, $provider, $ownerAccount)) && app('auth.driver')->login($ownerAccount, true);

        event(new LoggedIn($ownerAccount, $provider));

        return $doLinkOAuthAccount;
    }

    /**
     * @param string $token
     *
     * @return bool
     * @throws \App\Exceptions\Common\ValidationException
     * @throws \App\Exceptions\Users\TokenNotValidException
     */
    public function activate($token = null)
    {
        $data = !is_null($token) ? ['token' => $token] : $this->request->all();
        $validator = app('validator')->make($data, [
            'token' => 'required|string',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $activation = UserActivation::whereCode($data['token'])->first();
        if (!$activation) {
            throw new TokenNotValidException;
        }
        /** @var \App\Models\User $user */
        $user = User::findOrFail($activation->user_id);

        return $this->complete($user, $data['token']);
    }

    /**
     * @param integer $id
     *
     * @return boolean
     * @throws \App\Exceptions\Common\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete($id)
    {
        $validator = app('validator')->make($this->request->all(), [
            'password' => 'required|min:' . config('auth.passwords.users.min_length'),
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = $this->get($id);
        if (!app('hash')->check($this->request->input("password"), $user->password)) {
            throw new PasswordNotValidException;
        }

        return (bool)User::destroy($id);
    }

    /**
     * @param integer $id
     *
     * @return \App\Models\User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get($id)
    {
        if (!empty($user = User::findOrFail($id))) {
            return $user;
        }

        throw new ModelNotFoundException;
    }

    /**
     * @param integer $id
     *
     * @return boolean
     * @throws \App\Exceptions\Common\ValidationException
     */
    public function update($id)
    {
        $user = $this->get($id);

        $validator = app('validator')->make($this->request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $userBefore = clone $user;

        $this->request->has('name') && $user->name = $this->request->input("name");
        $user->email = $this->request->input("email");

        return $user->save() && event(new Updated($userBefore, $user)); // Fire the event on success only!
    }

    /**
     * @return bool|\App\Models\User
     * @throws \App\Exceptions\Common\ValidationException
     * @throws \App\Exceptions\Users\LoginNotValidException
     */
    public function login()
    {
        $validator = app('validator')->make($this->request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $this->request->only('email', 'password');

        if (app('auth.driver')->attempt($credentials, $this->request->has('remember'))) {
            $user = app('auth.driver')->user();

            event(new LoggedIn($user));

            return $user;
        }

        throw new LoginNotValidException($this->getFailedLoginMessage());
    }

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     *
     * @return bool
     */
    public function loginViaOAuth(SocialiteUser $oauthUserData, $provider)
    {
        /** @var UserOAuth $owningOAuthAccount */
        if ($owningOAuthAccount = UserOAuth::whereRemoteProvider($provider)->whereRemoteId($oauthUserData->id)->first()) {
            $ownerAccount = $owningOAuthAccount->owner;
            app('auth.driver')->login($ownerAccount, true);

            event(new LoggedIn($ownerAccount, $provider));

            return true;
        }

        return !$this->registerViaOAuth($oauthUserData, $provider) ? false : true;
    }

    /**
     * @return void
     */
    public function logout()
    {
        $user = app('auth.driver')->user();
        if (!empty($user)) {
            app('auth.driver')->logout();
            app('events')->fire(new LoggedOut($user));
        }
    }

    /**
     * @return boolean
     * @throws \App\Exceptions\Common\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function sendResetPasswordLinkViaEmail()
    {
        $validator = app('validator')->make($this->request->all(), [
            'email' => 'required|email|max:255'
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::whereEmail($this->request->only('email'))->first();
        if (is_null($user)) {
            throw new ModelNotFoundException(trans('passwords.user'));
        }

        event(new RequestedResetPasswordLink($user));

        return true;
    }

    /**
     * @return boolean
     * @throws \App\Exceptions\Common\ValidationException
     * @throws \App\Exceptions\Users\TokenNotValidException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function resetPassword()
    {
        $validator = app('validator')->make($this->request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:' . app('config')->get('auth.passwords.users.min_length')
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $this->request->only('email', 'password', 'password_confirmation', 'token');

        $passwordBroker = app('auth.password.broker');
        $response = $passwordBroker->reset(
            $credentials, function (User $user, $password) {
            $user->password = app('hash')->make($password);
            $user->save();
            app('auth.driver')->login($user);
        });

        switch ($response) {
            case $passwordBroker::INVALID_USER:
                throw new ModelNotFoundException(trans($response));
                break;
            case $passwordBroker::INVALID_TOKEN:
                throw new TokenNotValidException(trans($response));
                break;
        }

        event(new ResetPassword(app('auth.driver')->user()));

        return true;
    }

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     * @param User          $ownerAccount
     *
     * @return \App\Models\User|bool
     */
    private function linkOAuthAccount(SocialiteUser $oauthUserData, $provider, $ownerAccount)
    {
        /** @var UserOAuth[] $linkedAccounts */
        $linkedAccounts = $ownerAccount->linkedAccounts()->ofProvider($provider)->get();

        foreach ($linkedAccounts as $linkedAccount) {
            if ($linkedAccount->remote_id === $oauthUserData->id || $linkedAccount->email === $oauthUserData->email) {
                $linkedAccount->remote_id = $oauthUserData->id;
                $linkedAccount->nickname = $oauthUserData->nickname;
                $linkedAccount->name = $oauthUserData->name;
                $linkedAccount->email = $oauthUserData->email;
                $linkedAccount->avatar = $oauthUserData->avatar;

                return $linkedAccount->save() ? $ownerAccount : false;
            }
        }

        $linkedAccount = new UserOAuth();
        $linkedAccount->remote_provider = $provider;
        $linkedAccount->remote_id = $oauthUserData->id;
        $linkedAccount->nickname = $oauthUserData->nickname;
        $linkedAccount->name = $oauthUserData->name;
        $linkedAccount->email = $oauthUserData->email;
        $linkedAccount->avatar = $oauthUserData->avatar;

        return $ownerAccount->linkedAccounts()->save($linkedAccount) ? $ownerAccount : false;
    }
}

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
use App\Models\UserOAuth;
use Cartalyst\Sentinel\Activations\EloquentActivation;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Laravel\Socialite\AbstractUser as SocialiteUser;

class Registrar implements RegistrarContract
{
    use ValidatesRequests;

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var \Cartalyst\Sentinel\Users\UserRepositoryInterface|\Cartalyst\Sentinel\Users\IlluminateUserRepository
     */
    protected $userRepository;

    /**
     * @var \Cartalyst\Sentinel\Reminders\ReminderRepositoryInterface|\Cartalyst\Sentinel\Reminders\IlluminateReminderRepository
     */
    protected $reminderRepository;

    public function __construct()
    {
        $this->request = app('router')->getCurrentRequest();
        $this->userRepository = app('sentinel')->getUserRepository();
        $this->reminderRepository = app('sentinel')->getReminderRepository();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \Cartalyst\Sentinel\Users\UserInterface
     * @throws \App\Exceptions\Common\ValidationException
     */
    public function register()
    {
        $validator = app('validator')->make($this->request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:' . config('auth.password.min_length'),
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = new User();
        $this->request->has('name') && $user->name = $this->request->input('name');
        $user->email = $this->request->input('email');
        $user->password = $this->userRepository->getHasher()->hash($this->request->input('password'));
        $user->save() && app('events')->fire(new Registered($user));

        return $user;
    }

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     *
     * @return \Cartalyst\Sentinel\Users\UserInterface|bool
     */
    public function registerViaOAuth(SocialiteUser $oauthUserData, $provider)
    {
        if (!($ownerAccount = User::withTrashed()->whereEmail($oauthUserData->email)->first())) {
            $ownerAccount = \Eloquent::unguarded(function () use ($oauthUserData, $provider) {
                $user = User::create([
                    'name' => $oauthUserData->name,
                    'email' => $oauthUserData->email,
                    'password' => $this->userRepository->getHasher()->hash(uniqid("", true))
                ]);
                app('events')->fire(new Registered($user, $provider));

                return $user;
            });
        }

        # If user account is soft-deleted, restore it.
        $ownerAccount->trashed() && $ownerAccount->restore();

        # Update missing user name.
        if (!$ownerAccount->name) {
            $ownerAccount->name = $oauthUserData->name;
            $ownerAccount->save();
        }

        ($doLinkOAuthAccount = $this->linkOAuthAccount($oauthUserData, $provider, $ownerAccount)) && app('sentinel')->login($ownerAccount, true);

        app('events')->fire(new LoggedIn($ownerAccount, $provider));

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

        $activation = EloquentActivation::whereCode($data['token'])->first();
        if (!$activation) {
            throw new TokenNotValidException;
        }
        $user = $this->userRepository->findById($activation->user_id);

        return app('sentinel.activations')->complete($user, $data['token']);
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
            'password' => 'required|min:' . config('auth.password.min_length'),
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = $this->get($id);
        if (!$this->userRepository->getHasher()->check($this->request->input("password"), $user->password)) {
            throw new PasswordNotValidException;
        }

        return (bool)User::destroy($id);
    }

    /**
     * @param integer $id
     *
     * @return \App\Models\User|\Cartalyst\Sentinel\Users\UserInterface
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get($id)
    {
        if (!empty($user = $this->userRepository->findById($id))) {
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

        return $user->save() && app('events')->fire(new Updated($userBefore, $user)); // Fire the event on success only!
    }

    /**
     * @return boolean
     *
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

        if ($user = app('sentinel')->authenticate($credentials, $this->request->has('remember'))) {
            app('events')->fire(new LoggedIn($user));

            return true;
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
            app('sentinel')->login($ownerAccount, true);

            app('events')->fire(new LoggedIn($ownerAccount, $provider));

            return true;
        }

        return !$this->registerViaOAuth($oauthUserData, $provider) ? false : true;
    }

    /**
     * @return boolean
     */
    public function logout()
    {
        if ($user = app('sentinel')->getUser()) {
            app('events')->fire(new LoggedOut($user));
        }

        return app('sentinel')->logout();
    }

    /**
     * @return boolean
     *
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

        app('events')->fire(new RequestedResetPasswordLink($user));

        return true;
    }

    /**
     * @return boolean
     *
     * @throws \App\Exceptions\Common\ValidationException
     * @throws \App\Exceptions\Users\TokenNotValidException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function resetPassword()
    {
        $validator = app('validator')->make($this->request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:' . \Config::get('auth.password.min_length')
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $this->request->only('email', 'password', 'token');

        if (!($user = User::whereEmail($credentials['email'])->first())) {
            throw new ModelNotFoundException(trans('passwords.user'));
        }

        /** @var \Cartalyst\Sentinel\Reminders\EloquentReminder $reminder */
        $reminder = $this->reminderRepository->exists($user);
        if (!$reminder || $reminder->code !== $credentials['token']) {
            throw new TokenNotValidException(trans('passwords.token'));
        }

        app('sentinel.reminders')->complete($user, $credentials['token'], $credentials['password']) && app('events')->fire(new ResetPassword($user));

        return true;
    }

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     * @param User          $ownerAccount
     *
     * @return Authenticatable|bool
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

    /**
     * Get the failed login message.
     *
     * @return string
     */
    private function getFailedLoginMessage()
    {
        return 'These credentials do not match our records!';
    }
}

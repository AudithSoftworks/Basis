<?php namespace App\Services;

use App\Contracts\Registrar as RegistrarContract;
use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\LoginNotValidException;
use App\Exceptions\Users\PasswordNotValidException;
use App\Exceptions\Users\TokenNotValidException;
use App\Exceptions\Users\UserNotFoundException;
use App\Models\User;
use App\Models\UserOAuth;
use Illuminate\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Laravel\Socialite\AbstractUser as SocialiteUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The password broker implementation.
     *
     * @var PasswordBroker
     */
    protected $passwords;

    /**
     * @param Guard          $auth
     * @param PasswordBroker $password
     */
    public function __construct(Guard $auth, PasswordBroker $password)
    {
        $this->request = \Route::getCurrentRequest();
        $this->auth = $auth;
        $this->passwords = $password;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return Authenticatable
     */
    public function register()
    {
        $validator = \Validator::make($this->request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:' . \Config::get('auth.password.min-length'),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = new User();
        $this->request->has('name') && $user->name = $this->request->input('name');
        $user->email = $this->request->input('email');
        $user->password = \Hash::make($this->request->input('password'));
        $user->save();

        return $user;
    }

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     *
     * @return Authenticatable|bool
     */
    public function registerViaOAuth(SocialiteUser $oauthUserData, $provider)
    {
        if (!($ownerAccount = User::withTrashed()->whereEmail($oauthUserData->email)->first())) {
            $ownerAccount = \Eloquent::unguarded(function () use ($oauthUserData) {
                return User::create([
                    'name' => $oauthUserData->name,
                    'email' => $oauthUserData->email,
                    'password' => \Hash::make(uniqid("", true))
                ]);
            });
        }

        # If user account is soft-deleted, restore it.
        $ownerAccount->trashed() && $ownerAccount->restore();

        # Update missing user name.
        if (!$ownerAccount->name) {
            $ownerAccount->name = $oauthUserData->name;
            $ownerAccount->save();
        }

        return $this->linkOAuthAccount($oauthUserData, $provider, $ownerAccount);
    }

    /**
     * @param integer $id
     *
     * @return boolean
     *
     * @throws NotFoundHttpException
     * @throws PasswordNotValidException
     */
    public function delete($id)
    {
        /**
         * @var User $user
         */
        if (!($user = User::find($id))) {
            throw new NotFoundHttpException;
        }

        if (!\Hash::check($this->request->input("password"), $user->password)) {
            throw new PasswordNotValidException;
        }

        $user->destroy($id);

        return true;
    }

    /**
     * @param integer $id
     *
     * @return Authenticatable
     */
    public function get($id)
    {
        if (!($user = User::find($id))) {
            throw new NotFoundHttpException;
        }

        return $user;
    }

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function update($id)
    {
        /**
         * @var User $user
         */
        $user = $this->get($id);

        $validator = \Validator::make($this->request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'required|confirmed|min:' . \Config::get('auth.password.min-length'),
            'old_password' => 'required|min:' . \Config::get('auth.password.min-length'),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (!\Hash::check($this->request->input("old_password"), $user->password)) {
            throw new PasswordNotValidException;
        }

        $this->request->has('name') && $user->name = $this->request->input("name");
        $user->email = $this->request->input("email");
        $user->password = \Hash::make($this->request->input("password"));

        return $user->save();
    }

    /**
     * @return boolean
     *
     * @throws LoginNotValidException
     */
    public function login()
    {
        $validator = \Validator::make($this->request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $this->request->only('email', 'password');

        if ($this->auth->attempt($credentials, $this->request->has('remember'))) {
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
            $this->auth->login($ownerAccount, true);

            return true;
        }

        return !$this->registerViaOAuth($oauthUserData, $provider) ? false : true;
    }

    /**
     * @return boolean
     */
    public function logout()
    {
        $this->auth->logout();

        return true;
    }

    /**
     * @return boolean
     *
     * @throws NotFoundHttpException
     * @throws \UnexpectedValueException
     */
    public function sendResetPasswordLinkViaEmail()
    {
        $validator = \Validator::make($this->request->all(), [
            'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $attemptSendResetLink = $this->passwords->sendResetLink($this->request->only('email'), function (Message $message) {
            $message->subject($this->getPasswordResetEmailSubject());
        });

        switch ($attemptSendResetLink) {
            case PasswordBroker::RESET_LINK_SENT:
                return true;
            case PasswordBroker::INVALID_USER:
                throw new NotFoundHttpException;
            default:
                throw new \UnexpectedValueException;
        }
    }

    /**
     * @return boolean
     *
     * @throws ValidationException
     * @throws NotFoundHttpException
     * @throws TokenNotValidException
     * @throws \UnexpectedValueException
     */
    public function resetPassword()
    {
        $validator = \Validator::make($this->request->all(), [
            'token' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:' . \Config::get('auth.password.min-length')
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $this->request->only('email', 'password', 'password_confirmation', 'token');

        $attemptReset = $this->passwords->reset($credentials, function ($user, $password) {
            /**
             * @var \App\Models\User $user
             */
            $user->password = \Hash::make($password);
            $user->save();
        });

        switch ($attemptReset) {
            case PasswordBroker::PASSWORD_RESET:
                return true;
            case PasswordBroker::INVALID_USER:
                throw new UserNotFoundException;
            case PasswordBroker::INVALID_TOKEN:
                throw new TokenNotValidException;
            default:
                throw new \UnexpectedValueException(trans($attemptReset));
        }
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
        $linkedAccounts = $ownerAccount->linkedAccounts()->{$provider}()->get();

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
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    private function getPasswordResetEmailSubject()
    {
        return trans('passwords.reset_email_subject');
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    private function getFailedLoginMessage()
    {
        return 'These credentials do not match our records.';
    }
}

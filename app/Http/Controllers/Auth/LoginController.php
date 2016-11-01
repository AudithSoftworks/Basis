<?php namespace App\Http\Controllers\Auth;

use App\Events\Users\LoggedIn;
use App\Events\Users\LoggedOut;
use App\Events\Users\Registered;
use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\LoginNotValidException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOAuth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Factory as SocialiteContract;
use Laravel\Socialite\AbstractUser as SocialiteUser;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    private $redirectTo;

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        if (!empty($locale = app('translator')->getLocale()) && $locale != app('config')->get('app.locale')) {
            $this->redirectTo = '/' . $locale . $this->redirectTo;
        }

        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth/login');
    }

    /**
     * Log the user in.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\Common\ValidationException
     */
    public function loginViaWeb(Request $request)
    {
        $validator = app('validator')->make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->only('email', 'password');

        if (app('auth.driver')->attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            event(new LoggedIn($user = app('auth.driver')->user()));

            if ($request->expectsJson()) {
                return response()->json(['data' => $user]);
            }

            return redirect()->intended($this->redirectPath());
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if (!$lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        if ($request->expectsJson()) {
            throw new LoginNotValidException();
        }

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => app('translator')->get('auth.failed'),
            ]);
    }

    /**
     * @param \Laravel\Socialite\Contracts\Factory $socialite
     * @param string                               $provider
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleOAuthRedirect(SocialiteContract $socialite, $provider)
    {
        return $socialite->driver($provider)->redirect();
    }

    /**
     * Handle OAuth login.
     *
     * @param \Illuminate\Http\Request             $request
     * @param \Laravel\Socialite\Contracts\Factory $socialite
     * @param string                               $provider
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handleOAuthReturn(Request $request, SocialiteContract $socialite, $provider)
    {
        switch ($provider) {
            case 'google':
            case 'facebook':
                if (!$request->exists('code')) {
                    return redirect('/login')->withErrors(trans('passwords.oauth_failed'));
                }
                break;
            case 'twitter':
                if (!$request->exists('oauth_token') || !$request->exists('oauth_verifier')) {
                    return redirect('/login')->withErrors(trans('passwords.oauth_failed'));
                }
                break;
        }

        /** @var SocialiteUser $userInfo */
        $userInfo = $socialite->driver($provider)->user();
        if ($this->loginViaOAuth($userInfo, $provider)) {
            return redirect()->intended($this->redirectPath());
        }

        return redirect('/login')->withErrors(trans('passwords.oauth_failed'));
    }

    /**
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     *
     * @return bool
     */
    protected function loginViaOAuth(SocialiteUser $oauthUserData, $provider)
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
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|bool
     */
    protected function registerViaOAuth(SocialiteUser $oauthUserData, $provider)
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
     * @param SocialiteUser $oauthUserData
     * @param string        $provider
     * @param User          $ownerAccount
     *
     * @return \App\Models\User|bool
     */
    protected function linkOAuthAccount(SocialiteUser $oauthUserData, $provider, $ownerAccount)
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
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        if (app('auth.driver')->check()) {
            $user = app('auth.driver')->user();

            app('auth.driver')->logout();

            app('events')->fire(new LoggedOut($user));
        }

        $request->session()->flush();
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([]);
        }

        return redirect('/');
    }
}

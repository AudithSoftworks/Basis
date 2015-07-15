<?php namespace App\Http\Controllers\Users;

use App\Contracts\Registrar;
use App\Exceptions\Users\LoginNotValidException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Factory as SocialiteContract;
use Laravel\Socialite\AbstractUser as SocialiteUser;

class AuthController extends Controller
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The registrar implementation.
     *
     * @var Registrar
     */
    protected $registrar;

    /**
     * @var SocialiteContract
     */
    protected $socialite;

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @param  Guard             $auth
     * @param  Registrar         $registrar
     * @param  SocialiteContract $socialite
     */
    public function __construct(Guard $auth, Registrar $registrar, SocialiteContract $socialite)
    {
        $this->auth = $auth;
        $this->registrar = $registrar;
        $this->request = \Route::getCurrentRequest();
        $this->socialite = $socialite;
        if (!empty($locale = \Lang::getLocale()) && $locale != \Config::get('app.locale')) {
            $this->redirectTo = '/' . $locale . $this->redirectTo;
        }

        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Handle OAuth login.
     *
     * @param string $provider
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function getOAuth($provider)
    {
        switch ($provider) {
            case 'google':
            case 'facebook':
                if (!$this->request->exists('code')) {
                    return redirect('/login')->withErrors(trans('passwords.oauth_failed'));
                }
                break;
            case 'twitter':
                if (!$this->request->exists('oauth_token') || !$this->request->exists('oauth_verifier')) {
                    return redirect('/login')->withErrors(trans('passwords.oauth_failed'));
                }
                break;
        }

        /** @var SocialiteUser $userInfo */
        $userInfo = $this->socialite->driver($provider)->user();
        if ($this->registrar->loginViaOAuth($userInfo, $provider)) {
            if ($this->request->ajax() || $this->request->wantsJson()) {
                return ['message' => 'Login successful']; // TODO: Move to API app (Lumen based?)
            }

            return redirect()->intended($this->redirectPath());
        }

        if ($this->request->ajax() || $this->request->wantsJson()) {
            throw new LoginNotValidException(trans('passwords.oauth_failed')); // TODO: Move to API app (Lumen based?)
        }

        return redirect('/login')->withErrors(trans('passwords.oauth_failed'));
    }

    /**
     * Show the application login form.
     *
     * @param string $provider
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\View\View
     */
    public function getLogin($provider = null)
    {
        if (!is_null($provider)) {
            return $this->socialite->driver($provider)->redirect();
        }

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Ready'];
        }

        return view('auth/login');
    }

    /**
     * Handle a login request to the application.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postLogin()
    {
        $this->registrar->login();

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Login successful'];
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLogout()
    {
        $this->registrar->logout();

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Logout successful'];
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    private function redirectPath()
    {
        if (isset($this->redirectPath)) {
            return $this->redirectPath;
        }

        return isset($this->redirectTo) ? $this->redirectTo : '/';
    }
}

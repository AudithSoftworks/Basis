<?php namespace App\Http\Controllers\Users;

use App\Contracts\Registrar;
use App\Exceptions\Users\LoginNotValidException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Factory as SocialiteContract;
use Laravel\Socialite\AbstractUser as SocialiteUser;

class AuthController extends Controller
{
    private $redirectTo;

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        if (!empty($locale = app('translator')->getLocale()) && $locale != app('config')->get('app.locale')) {
            $this->redirectTo = '/' . $locale . $this->redirectTo;
        }

        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Handle OAuth login.
     *
     * @param \Illuminate\Http\Request             $request
     * @param \App\Contracts\Registrar             $registrar
     * @param \Laravel\Socialite\Contracts\Factory $socialite
     * @param string                               $provider
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function getOAuth(Request $request, Registrar $registrar, SocialiteContract $socialite, $provider)
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
        if ($registrar->loginViaOAuth($userInfo, $provider)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Login successful']); // TODO: Move to API app (Lumen based?)
            }

            return redirect()->intended($this->redirectPath());
        }

        if ($request->ajax() || $request->wantsJson()) {
            throw new LoginNotValidException(trans('passwords.oauth_failed')); // TODO: Move to API app (Lumen based?)
        }

        return redirect('/login')->withErrors(trans('passwords.oauth_failed'));
    }

    /**
     * Show the application login form.
     *
     * @param \Illuminate\Http\Request             $request
     * @param \Laravel\Socialite\Contracts\Factory $socialite
     * @param string                               $provider
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getLogin(Request $request, SocialiteContract $socialite, $provider = null)
    {
        if (!is_null($provider)) {
            return $socialite->driver($provider)->redirect();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Ready']);
        }

        return view('auth/login');
    }

    /**
     * Log the user in.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Registrar $registrar
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function postLogin(Request $request, Registrar $registrar)
    {
        $user = $registrar->login();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Login successful', 'data' => $user]);
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Registrar $registrar
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function getLogout(Request $request, Registrar $registrar)
    {
        $registrar->logout();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Logout successful']);
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

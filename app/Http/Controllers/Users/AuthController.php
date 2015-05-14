<?php namespace App\Http\Controllers\Users;

use App\Contracts\Registrar;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Show the application login form.
     *
     * @param string $provider
     *
     * @return Response
     */
    public function getLogin($provider = null)
    {
        if (!is_null($provider)) {
            switch ($provider) {
                case 'google':
                    if ($this->request->exists('code')) {
                        break;
                    }
                case 'twitter':
                    if ($this->request->exists('oauth_token') && $this->request->exists('oauth_verifier')) {
                        break;
                    }
                default:
                    return $this->socialite->driver($provider)->redirect();
            }

            /** @var SocialiteUser $userInfo */
            $userInfo = $this->socialite->driver($provider)->user();
            if ($this->registrar->loginViaOAuth($userInfo, $provider)) {
                if ($this->request->ajax() || $this->request->wantsJson()) {
                    return ['message' => 'Login successful'];
                }

                return redirect('/home');
            }
        }

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Ready'];
        }

        return view('auth/login');
    }

    /**
     * Handle a login request to the application.
     *
     * @return Response
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
     * @return Response
     */
    public function getLogout()
    {
        $this->registrar->logout();

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Logout successful'];
        }

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/home');
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    private function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}

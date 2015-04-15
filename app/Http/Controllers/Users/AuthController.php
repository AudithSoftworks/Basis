<?php namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Audith\Contracts\Registrar;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Create a new authentication controller instance.
     *
     * @param  Guard     $auth
     * @param  Registrar $registrar
     * @param  Request   $request
     */
    public function __construct(Guard $auth, Registrar $registrar, Request $request)
    {
        $this->auth = $auth;
        $this->registrar = $registrar;
        $this->request = $request;

        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Show the application login form.
     *
     * @return Response
     */
    public function getLogin()
    {
        if ($this->request->ajax() or $this->request->wantsJson()) {
            return ['message' => 'Ready'];
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @return Response
     */
    public function postLogin()
    {
        if ($this->registrar->login()) {
            if ($this->request->ajax() or $this->request->wantsJson()) {
                return ['message' => 'Login successful'];
            }

            return redirect()->intended($this->redirectPath());
        }

        return redirect($this->loginPath())
            ->withInput($this->request->only('email', 'remember'))
            ->withErrors([
                'email' => $this->getFailedLoginMessage(),
            ]);
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function getLogout()
    {
        $this->registrar->logout();

        if ($this->request->ajax() or $this->request->wantsJson()) {
            return ['message' => 'Logout successful'];
        }

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
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

    /**
     * Get the path to the login route.
     *
     * @return string
     */
    private function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
    }
}

<?php namespace App\Http\Controllers\Users;

use App\Exceptions\Users\LoginNotValidException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
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
     * Create a new authentication controller instance.
     *
     * @param  Guard     $auth
     * @param  Registrar $registrar
     */
    public function __construct(Guard $auth, Registrar $registrar)
    {
        $this->auth = $auth;
        $this->registrar = $registrar;

        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Show the application login form.
     *
     * @return Response
     */
    public function getLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function postLogin(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $this->throwValidationException($request, $validator);
            }

            $credentials = $request->only('email', 'password');

            if ($this->auth->attempt($credentials, $request->has('remember'))) {
                return response('', 200);

                //return redirect()->intended($this->redirectPath());
            }

            throw new LoginNotValidException;

            /*
            return redirect($this->loginPath())
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => $this->getFailedLoginMessage(),
                ]);
            */
        } catch (LoginNotValidException $e) {
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ])->setStatusCode(422);
        } catch (\Exception $e) {
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function getLogout()
    {
        $this->auth->logout();

        return response('', 200);

        //return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
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

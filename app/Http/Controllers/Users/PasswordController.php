<?php namespace App\Http\Controllers\Users;

use App\Contracts\Registrar;
use App\Exceptions\Users\TokenNotValidException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PasswordController extends Controller
{
    /**
     * Registrar service instance.
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
     * Create a new password controller instance.
     *
     * @param  Registrar                                 $registrar
     */
    public function __construct(Registrar $registrar)
    {
        $this->registrar = $registrar;
        $this->request = app('router')->getCurrentRequest();

        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Response
     */
    public function getEmail()
    {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            return [];
        }

        return view('password/email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @return \Response
     */
    public function postEmail()
    {
        $this->registrar->sendResetPasswordLinkViaEmail();

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Password reset request received'];
        }

        return redirect()->back()->with('message', trans('passwords.sent'));
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string $token
     *
     * @return \Response
     *
     * @throws NotFoundHttpException
     */
    public function getReset($token = null)
    {
        if (is_null($token)) {
            if ($this->request->ajax() || $this->request->wantsJson()) {
                throw new TokenNotValidException();
            }

            return view('password/reset')->withErrors(['token' => trans(PasswordBroker::INVALID_TOKEN)]);
        }

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['token' => $token];
        }

        return view('password/reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @return \Response
     */
    public function postReset()
    {
        $this->registrar->resetPassword();

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Password successfully reset'];
        }

        return redirect($this->redirectPath())->with('message', trans('passwords.reset'));
    }

    /**
     * Get the post-register/-login redirect path.
     *
     * @return string
     */
    private function redirectPath()
    {
        if (isset($this->redirectPath)) {
            return $this->redirectPath;
        }

        return isset($this->redirectTo) ? $this->redirectTo : '/login';
    }
}

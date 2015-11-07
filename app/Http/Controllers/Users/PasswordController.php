<?php namespace App\Http\Controllers\Users;

use App\Contracts\Registrar;
use App\Exceptions\Users\TokenNotValidException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;

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
     * @param  Registrar $registrar
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
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function getEmail()
    {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            return response([]);
        }

        return view('password/email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function postEmail()
    {
        $this->registrar->sendResetPasswordLinkViaEmail();

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return response(['message' => 'Password reset request received']);
        }

        return redirect()->back()->with('message', trans('passwords.sent'));
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\Users\TokenNotValidException
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
            return response(['token' => $token]);
        }

        return view('password/reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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

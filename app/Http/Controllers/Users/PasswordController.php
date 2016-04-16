<?php namespace App\Http\Controllers\Users;

use App\Contracts\Registrar;
use App\Exceptions\Users\TokenNotValidException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function requestPasswordResetLink(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([]);
        }

        return view('password/email');
    }

    /**
     * Send a password reset link to the given email's owner, via email.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Registrar $registrar
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function sendPasswordResetLink(Request $request, Registrar $registrar)
    {
        $registrar->sendResetPasswordLinkViaEmail();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => trans('passwords.sent')]);
        }

        return redirect()->back()->with('message', trans('passwords.sent'));
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $token
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showPasswordResetForm(Request $request, $token = null)
    {
        if (is_null($token)) {
            if ($request->ajax() || $request->wantsJson()) {
                throw new TokenNotValidException();
            }

            return view('password/reset')->withErrors(['token' => trans(PasswordBroker::INVALID_TOKEN)]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['token' => $token]);
        }

        return view('password/reset')->with('token', $token);
    }

    /**
     * Reset the password through password-reset-token and email provided.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Registrar $registrar
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request, Registrar $registrar)
    {
        $registrar->resetPassword();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => trans('passwords.reset')]);
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

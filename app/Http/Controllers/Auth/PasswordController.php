<?php namespace App\Http\Controllers\Auth;

use App\Events\Users\RequestedResetPasswordLink;
use App\Events\Users\ResetPassword;
use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\TokenNotValidException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    use RedirectsUsers;

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
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function requestPasswordResetLink()
    {
        return view('password/email');
    }

    /**
     * Send a password reset link to the given email's owner, via email.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\Common\ValidationException
     */
    public function sendPasswordResetLink(Request $request)
    {
        $validator = app('validator')->make($request->all(), [
            'email' => 'required|email|max:255'
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::whereEmail($request->only('email'))->first();
        if (is_null($user)) {
            throw new ModelNotFoundException(trans('passwords.user'));
        }

        $user->notify(new ResetPasswordNotification($token = app('auth.password.broker')->createToken($user)));

        event(new RequestedResetPasswordLink($user));

        if ($request->expectsJson()) {
            $response = ['message' => trans('passwords.sent')];
            if (env('APP_ENV') == 'testing') {
                $response['token'] = $token;
            }

            return response()->json($response);
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
            if ($request->expectsJson()) {
                throw new TokenNotValidException();
            }

            return view('password/reset')->withErrors(['token' => trans(PasswordBroker::INVALID_TOKEN)]);
        }

        if ($request->expectsJson()) {
            return response()->json(['token' => $token]);
        }

        return view('password/reset')->with('token', $token);
    }

    /**
     * Reset the password through password-reset-token and email provided.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\Common\ValidationException
     */
    public function resetPassword(Request $request)
    {
        $validator = app('validator')->make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:' . app('config')->get('auth.passwords.users.min_length')
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');

        $passwordBroker = app('auth.password.broker');
        $response = $passwordBroker->reset(
            $credentials, function (User $user, $password) {
            $user->password = app('hash')->make($password);
            $user->save();
            app('auth.driver')->login($user);
        });

        switch ($response) {
            case $passwordBroker::INVALID_USER:
                throw new ModelNotFoundException(trans($response));
                break;
            case $passwordBroker::INVALID_TOKEN:
                throw new TokenNotValidException(trans($response));
                break;
        }

        event(new ResetPassword(app('auth.driver')->user()));

        if ($request->expectsJson()) {
            return response()->json(['message' => trans('passwords.reset')]);
        }

        return redirect($this->redirectPath())->with('message', trans('passwords.reset'));
    }
}

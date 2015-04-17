<?php namespace App\Http\Controllers\Users;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\Users\TokenNotValidException;
use App\Http\Controllers\Controller;

class PasswordController extends Controller
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The password broker implementation.
     *
     * @var PasswordBroker
     */
    protected $passwords;

    /**
     * Create a new password controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard          $auth
     * @param  \Illuminate\Contracts\Auth\PasswordBroker $passwords
     */
    public function __construct(Guard $auth, PasswordBroker $passwords)
    {
        $this->auth = $auth;
        $this->passwords = $passwords;

        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Response
     */
    public function getEmail()
    {
        return response('', 200);

        // @todo
        // return view('auth.password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  Request $request
     *
     * @return \Response
     */
    public function postEmail(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'email' => 'required|email|max:255'
            ]);

            if ($validator->fails()) {
                $this->throwValidationException($request, $validator);
            }

            $attemptSendResetLink = $this->passwords->sendResetLink($request->only('email'), function (Message $message) {
                $message->subject($this->getEmailSubject());
            });

            switch ($attemptSendResetLink) {
                case PasswordBroker::RESET_LINK_SENT:
                    if (\App::environment() == 'testing') {
                        $token = \DB::table('password_resets')->where('email', '=', 'shehi@imanov.me')->pluck('token');

                        return response()->json(['message' => trans($attemptSendResetLink), 'token' => $token])->setStatusCode(200);
                    }

                    return response()->json(['message' => trans($attemptSendResetLink)])->setStatusCode(200);
                case PasswordBroker::INVALID_USER:
                    throw new NotFoundHttpException;
                default:
                    throw new \Exception;
            }
        } catch (NotFoundHttpException $e) {
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ])->setStatusCode(404);
        } catch (HttpResponseException $e) {
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
            throw new NotFoundHttpException;
        }

        return response()->json(['token' => $token])->setStatusCode(200);
        //return view('auth.reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param  Request $request
     *
     * @return \Response
     *
     * @throws NotFoundHttpException
     * @throws TokenNotValidException
     * @throws HttpResponseException
     * @throws \Exception
     */
    public function postReset(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email|max:255',
                'password' => 'required|confirmed|min:' . \Config::get('auth.password.min-length')
            ]);

            if ($validator->fails()) {
                $this->throwValidationException($request, $validator);
            }

            $credentials = $request->only('email', 'password', 'password_confirmation', 'token');

            $attemptReset = $this->passwords->reset($credentials, function ($user, $password) {
                /**
                 * @var \App\Models\User $user
                 */
                $user->password = \Hash::make($password);
                $user->save();
            });

            switch ($attemptReset) {
                case PasswordBroker::PASSWORD_RESET:
                    return response()->json(['message' => 'Password successfully reset'])->setStatusCode(200);
                case PasswordBroker::INVALID_USER:
                    throw new NotFoundHttpException;
                case PasswordBroker::INVALID_TOKEN:
                    throw new TokenNotValidException;
                default:
                    throw new \Exception(['email' => trans($attemptReset)]);
            }
        } catch (NotFoundHttpException $e) {
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ])->setStatusCode(404);
        } catch (HttpResponseException $e) {
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
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    private function getEmailSubject()
    {
        return trans('passwords.reset_email_subject');
    }
}

<?php namespace App\Http\Controllers\Auth;

use App\Events\Users\RequestedActivationLink;
use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\TokenNotValidException;
use App\Exceptions\Users\UserAlreadyActivatedException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivation;
use App\Traits\Users\Activates;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;

class ActivateController extends Controller
{
    use Activates, RedirectsUsers;

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['requestActivationCode']]);
    }

    /**
     * Request account activation link via email.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Users\UserAlreadyActivatedException
     */
    public function requestActivationCode(Request $request)
    {
        /** @var User $user */
        $user = app('auth.driver')->user();

        if (false !== $this->completed($user)) {
            throw new UserAlreadyActivatedException;
        }

        app('events')->fire(new RequestedActivationLink($user));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Activation link sent']);
        }

        return redirect()->back()->with('message', 'Activation link sent');
    }

    /**
     * Activate an account [Web only].
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $token
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \App\Exceptions\Common\ValidationException
     */
    public function activate(Request $request, $token = null)
    {
        $data = !is_null($token) ? ['token' => $token] : $request->all();
        $validator = app('validator')->make($data, [
            'token' => 'required|string',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $activation = UserActivation::whereCode($data['token'])->first();
        if (!$activation) {
            throw new TokenNotValidException;
        }
        /** @var \App\Models\User $user */
        $user = User::findOrFail($activation->user_id);

        $this->complete($user, $data['token']);

        return ($request->expectsJson()) ? response()->json(['message' => 'Activated']) : redirect($this->redirectPath())->with('message', 'Activation successful');
    }
}

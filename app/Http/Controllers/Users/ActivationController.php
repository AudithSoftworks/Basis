<?php namespace App\Http\Controllers\Users;

use App\Contracts\Registrar;
use App\Events\Users\RequestedActivationLink;
use App\Exceptions\Common\NotImplementedException;
use App\Exceptions\Users\UserAlreadyActivatedException;
use App\Exceptions\Users\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Users\Activates;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    use Activates;

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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Activation link sent']);
        }

        return redirect()->back()->with('message', 'Activation link sent');
    }

    /**
     * Activate an account [Web only].
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Registrar $registrar
     * @param string|null              $token
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\Http\JsonResponse
     */
    public function activate(Request $request, Registrar $registrar, $token = null)
    {
        switch ($requestMethod = $request->getMethod()) {
            case 'GET':
                if ($request->ajax() || $request->wantsJson()) {
                    throw new NotImplementedException;
                }
                break;
            case 'POST':
                if (!$request->ajax() && !$request->wantsJson()) {
                    throw new NotImplementedException;
                }
                break;
        }

        $registrar->activate($token);

        return $requestMethod == 'GET' ? redirect($this->redirectPath())->with('message', 'Activation successful') : response()->json(['message' => 'Activated']);
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

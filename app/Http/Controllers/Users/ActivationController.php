<?php namespace App\Http\Controllers\Users;

use App\Contracts\Registrar;
use App\Events\Users\RequestedActivationLink;
use App\Exceptions\Common\NotImplementedException;
use App\Exceptions\Users\UserAlreadyActivatedException;
use App\Exceptions\Users\UserNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['getCode']]);
    }

    /**
     * Request account activation link via email.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \App\Exceptions\Users\UserAlreadyActivatedException
     */
    public function getCode(Request $request)
    {
        if (!($user = app('sentinel')->getUser())) {
            throw new UserNotFoundException;
        }

        if (false !== app('sentinel.activations')->completed($user)) {
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
     * @param string                   $token
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getProcess(Request $request, Registrar $registrar, $token)
    {
        if ($request->ajax() || $request->wantsJson()) {
            throw new NotImplementedException;
        }

        $registrar->activate($token);

        return redirect($this->redirectPath())->with('message', 'Activation successful');
    }

    /**
     * Activate an account [JSON-API only].
     *
     * @param \Illuminate\Http\Request $request     *
     * @param \App\Contracts\Registrar $registrar
     *
     * @return array
     */
    public function postProcess(Request $request, Registrar $registrar)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            throw new NotImplementedException;
        }

        $registrar->activate();

        return ['message' => 'Activated'];
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

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
     * @param  Registrar $registrar
     */
    public function __construct(Registrar $registrar)
    {
        $this->registrar = $registrar;
        $this->request = app('router')->getCurrentRequest();

        $this->middleware('auth', ['only' => ['getCode']]);
    }

    /**
     * Request account activation link via email.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \App\Exceptions\Users\UserAlreadyActivatedException
     * @throws \App\Exceptions\Users\UserNotFoundException
     */
    public function getCode()
    {
        if (!($user = app('sentinel')->getUser())) {
            throw new UserNotFoundException;
        }

        if (false !== app('sentinel.activations')->completed($user)) {
            throw new UserAlreadyActivatedException;
        }

        app('events')->fire(new RequestedActivationLink($user));

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Activation link sent'];
        }

        return redirect()->back()->with('message', 'Activation link sent');
    }

    /**
     * Activate an account [Web only].
     *
     * @param string $token
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getProcess($token)
    {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            throw new NotImplementedException;
        }

        $this->registrar->activate($token);

        return redirect($this->redirectPath())->with('message', 'Activation successful');
    }

    /**
     * Activate an account [JSON-API only].
     *
     * @return array
     */
    public function postProcess()
    {
        if (!$this->request->ajax() && !$this->request->wantsJson()) {
            throw new NotImplementedException;
        }

        $this->registrar->activate();

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

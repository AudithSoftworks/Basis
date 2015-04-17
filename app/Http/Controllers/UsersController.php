<?php namespace App\Http\Controllers;

use App\Exceptions\Users\PasswordNotValidException;
use App\Models\User;
use Audith\Contracts\Registrar;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends Controller
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
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Create a new authentication controller instance.
     *
     * @param  Guard     $auth
     * @param  Registrar $registrar
     * @param  Request   $request
     */
    public function __construct(Guard $auth, Registrar $registrar, Request $request)
    {
        $this->auth = $auth;
        $this->registrar = $registrar;
        $this->request = $request;

        $this->middleware('guest', ['except' => ['edit', 'update', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @throws \BadMethodCallException
     */
    public function index()
    {
        throw new \BadMethodCallException;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return array
     */
    public function create()
    {
        if ($this->request->ajax() or $this->request->wantsJson()) {
            return ['message' => 'Ready'];
        }

        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return array
     */
    public function store()
    {
        $user = $this->registrar->register();

        if ($this->request->ajax() or $this->request->wantsJson()) {
            return ['message' => 'Created'];
        }

        $this->auth->login($user);

        return redirect($this->redirectPath());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return array
     */
    public function show($id)
    {
        /**
         * @var User $user
         */
        $user = $this->registrar->get($id);
        if ($this->request->ajax() or $this->request->wantsJson()) {
            return ['message' => 'Found', 'data' => $user->toJson()];
        }

        // TODO Create appropriate Views for non-JSON requests
        return;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Response
     */
    public function edit($id)
    {
        $user = $this->registrar->get($id);
        if ($this->request->ajax() or $this->request->wantsJson()) {
            return ['message' => 'Ready', 'data' => $user->toJson()];
        }

        // TODO Create appropriate Views for non-JSON requests
        return;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int     $id
     *
     * @return \Response
     *
     * @throws NotFoundHttpException
     * @throws PasswordNotValidException
     */
    public function update($id)
    {
        $this->registrar->update($id);
        if ($this->request->ajax() or $this->request->wantsJson()) {
            return ['message' => 'Updated'];
        }

        // TODO Create appropriate Views for non-JSON requests
        return;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return array
     */
    public function destroy($id)
    {
        $this->registrar->delete($id);

        if ($this->request->ajax() or $this->request->wantsJson()) {
            return ['message' => 'Deleted'];
        }

        return redirect($this->redirectPath()); // TODO Redirection path might need a fix.
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
}

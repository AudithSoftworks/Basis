<?php namespace App\Http\Controllers;

use App\Contracts\Registrar;
use App\Exceptions\Common\NotImplementedException;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends Controller
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
        $this->request = \Route::getCurrentRequest();

        $this->middleware('guest', ['except' => ['show', 'edit', 'update', 'destroy']]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return array
     */
    public function create()
    {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            throw new NotImplementedException;
        }

        return view('auth/register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return array
     */
    public function store()
    {
        $this->registrar->register();

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Created'];
        }

        return redirect($this->redirectPath())->with('message', 'Created');
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
        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Found', 'data' => $user->toArray()];
        }

        return ['message' => 'Found', 'data' => $user->toArray()]; // @todo Create appropriate Views for non-JSON requests
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
        /** @var \App\Models\User $user */
        $user = $this->registrar->get($id);
        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Ready', 'data' => $user->toArray()];
        }

        return view('auth/edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int     $id
     *
     * @return \Response
     *
     * @throws NotFoundHttpException
     */
    public function update($id)
    {
        $this->registrar->update($id);
        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Updated'];
        }

        return redirect()->back()->with('message', 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return array
     *
     * @throws \App\Exceptions\Users\PasswordNotValidException
     */
    public function destroy($id)
    {
        $this->registrar->delete($id);

        if ($this->request->ajax() || $this->request->wantsJson()) {
            return ['message' => 'Deleted'];
        }

        return redirect($this->redirectPath())->with('message', 'Deleted');
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

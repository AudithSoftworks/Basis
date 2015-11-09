<?php namespace App\Http\Controllers;

use App\Contracts\Registrar;
use App\Exceptions\Common\NotImplementedException;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['show', 'edit', 'update', 'destroy']]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            throw new NotImplementedException;
        }

        return view('auth/register');
    }

    /**
     * Store a newly created user.
     *
     * @param \Illuminate\Http\Request $request *
     * @param \App\Contracts\Registrar $registrar
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request, Registrar $registrar)
    {
        $user = $registrar->register();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Created', 'data' => $user])->setStatusCode(\Illuminate\Http\Response::HTTP_CREATED);
        }

        return redirect($this->redirectPath())->with('message', 'Created');
    }

    /**
     * Display the specified user information.
     *
     * @param \App\Contracts\Registrar $registrar
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Registrar $registrar, $id)
    {
        $user = $registrar->get($id);

        return response()->json(['message' => 'Found', 'data' => $user]);
    }

    /**
     * Show the form for editing user information.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Registrar $registrar
     * @param int                      $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Request $request, Registrar $registrar, $id)
    {
        /** @var \App\Models\User $user */
        $user = $registrar->get($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Ready', 'data' => $user]);
        }

        return view('auth/edit', ['user' => $user]);
    }

    /**
     * Update the specified user information.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Registrar $registrar
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Registrar $registrar, $id)
    {
        $registrar->update($id);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Updated']);
        }

        return redirect()->back()->with('message', 'Updated');
    }

    /**
     * Remove the specified user record from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Registrar $registrar
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request, Registrar $registrar, $id)
    {
        $registrar->delete($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Deleted']);
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

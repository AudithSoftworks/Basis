<?php namespace App\Http\Controllers;

use App\Exceptions\Users\LoginRequiredException;
use App\Exceptions\Users\PasswordRequiredException;
use App\Exceptions\Users\UserNotFoundException;
use App\Exceptions\Users\PasswordNotValidException;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Response
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
     * @return \Response
     */
    public function create()
    {
        return response()->json(['message' => 'Ready']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return \Response
     */
    public function store(Request $request)
    {
        try {
            if (!$request->has('email')) {
                throw new LoginRequiredException;
            } elseif (!$request->has('password')) {
                throw new PasswordRequiredException;
            }

            $user = new User();
            $user->email = $request->input("email");
            $user->password = \Hash::make($request->input("password"));
            $user->save();

            return response()->json(['message' => 'Created'])->setStatusCode(201); // HTTP/1.1 201 Created
        } catch (\Exception $e) {
            return response()->json(['message' => get_class($e)])->setStatusCode(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Response
     */
    public function show($id)
    {
        try {
            if ($user = User::find($id)) {
                return response()->json(['message' => 'Found', 'data' => $user->toJson()]);
            } else {
                return response()->json(['message' => 'Not Found'])->setStatusCode(404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => get_class($e)])->setStatusCode(500);
        }
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
        try {
            if ($user = User::find($id)) {
                return response()->json(['message' => 'Ready', 'data' => $user->toJson()]);
            } else {
                return response()->json(['message' => 'Not Found'])->setStatusCode(404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => get_class($e)])->setStatusCode(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Response
     *
     * @throws UserNotFoundException
     * @throws PasswordNotValidException
     */
    public function update(Request $request, $id)
    {
        try {
            /**
             * @var User $user
             */
            if (!($user = User::find($id))) {
                throw new UserNotFoundException;
            }

            if (!\Hash::check($request->input("currentPassword"), $user->password)) {
                throw new PasswordNotValidException;
            }

            $user->email = $request->input("email");
            $user->password = \Hash::make($request->input("password"));
            $user->save();

            return response()->json(['message' => 'Updated'])->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['message' => get_class($e)])->setStatusCode(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            /**
             * @var User $user
             */
            if (!($user = User::find($id))) {
                throw new UserNotFoundException;
            }

            if (!\Hash::check($request->input("password"), $user->password)) {
                throw new PasswordNotValidException;
            }

            $user->destroy($id);

            return response()->json(['message' => 'Deleted'])->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['message' => get_class($e)])->setStatusCode(500);
        }
    }
}

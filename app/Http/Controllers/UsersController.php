<?php namespace App\Http\Controllers;

use App\Exceptions\Users\PasswordNotValidException;
use Illuminate\Http\Exception\HttpResponseException;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            $validator = \Validator::make($request->all(), [
                'name' => 'sometimes|required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|confirmed|min:' . \Config::get('auth.password.min-length'),
            ]);

            if ($validator->fails()) {
                $this->throwValidationException($request, $validator);
            }

            $user = new User();
            $request->has('name') && $user->name = $request->input("name");
            $user->email = $request->input("email");
            $user->password = \Hash::make($request->input("password"));
            $user->save();

            return response()->json(['message' => 'Created'])->setStatusCode(201); // HTTP/1.1 201 Created
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
            }

            throw new NotFoundHttpException;
        } catch (NotFoundHttpException $e) {
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ])->setStatusCode(500);
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
            }

            throw new NotFoundHttpException;
        } catch (NotFoundHttpException $e) {
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ])->setStatusCode(404);
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
     * @throws NotFoundHttpException
     * @throws PasswordNotValidException
     */
    public function update(Request $request, $id)
    {
        try {
            /**
             * @var User $user
             */
            if (!($user = User::find($id))) {
                throw new NotFoundHttpException;
            }

            $validator = \Validator::make($request->all(), [
                'name' => 'sometimes|required|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'password' => 'required|confirmed|min:' . \Config::get('auth.password.min-length'),
                'old_password' => 'required|min:' . \Config::get('auth.password.min-length'),
            ]);

            if ($validator->fails()) {
                $this->throwValidationException($request, $validator);
            }

            if (!\Hash::check($request->input("old_password"), $user->password)) {
                throw new PasswordNotValidException;
            }

            $request->has('name') && $user->name = $request->input("name");
            $user->email = $request->input("email");
            $user->password = \Hash::make($request->input("password"));
            $user->save();

            return response()->json(['message' => 'Updated'])->setStatusCode(200);
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
                throw new NotFoundHttpException;
            }

            if (!\Hash::check($request->input("password"), $user->password)) {
                throw new PasswordNotValidException;
            }

            $user->destroy($id);

            return response()->json(['message' => 'Deleted'])->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

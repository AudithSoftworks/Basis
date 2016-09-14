<?php namespace App\Http\Controllers\Auth;

use App\Events\Users\Registered;
use App\Exceptions\Common\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;

class RegisterController extends Controller
{
    use RedirectsUsers;

    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth/register');
    }

    /**
     * Store a newly created user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\Common\ValidationException
     */
    public function register(Request $request)
    {
        $validator = app('validator')->make($request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:' . config('auth.passwords.users.min_length'),
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = new User();
        $request->has('name') && $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = app('hash')->make($request->input('password'));
        $user->save() && event(new Registered($user));

        app('auth.driver')->login($user);

        return ($request->expectsJson())
            ? response()->json($user)->setStatusCode(IlluminateResponse::HTTP_CREATED)
            : redirect($this->redirectPath());
    }
}

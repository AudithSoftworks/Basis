<?php namespace App\Services;

use App\Exceptions\Common\ValidationException;
use App\Exceptions\Users\LoginNotValidException;
use App\Exceptions\Users\PasswordNotValidException;
use App\Models\User;
use Audith\Contracts\Registrar as RegistrarContract;
use Illuminate\Auth\Guard;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Registrar implements RegistrarContract
{
    use ValidatesRequests;

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    public function __construct(Request $request, Guard $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function register()
    {
        $validator = \Validator::make($this->request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:' . \Config::get('auth.password.min-length'),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = new User();
        $this->request->has('name') && $user->name = $this->request->input('name');
        $user->email = $this->request->input('email');
        $user->password = \Hash::make($this->request->input('password'));
        $user->save();

        return $user;
    }

    /**
     * @param integer $id
     *
     * @return boolean
     *
     * @throws NotFoundHttpException
     * @throws PasswordNotValidException
     */
    public function delete($id)
    {
        /**
         * @var User $user
         */
        if (!($user = User::find($id))) {
            throw new NotFoundHttpException;
        }

        if (!\Hash::check($this->request->input("password"), $user->password)) {
            throw new PasswordNotValidException;
        }

        $user->destroy($id);

        return true;
    }

    /**
     * @param integer $id
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function get($id)
    {
        if (!($user = User::find($id))) {
            throw new NotFoundHttpException;
        }

        return $user;
    }

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function update($id)
    {
        /**
         * @var User $user
         */
        $user = $this->get($id);

        $validator = \Validator::make($this->request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'required|confirmed|min:' . \Config::get('auth.password.min-length'),
            'old_password' => 'required|min:' . \Config::get('auth.password.min-length'),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (!\Hash::check($this->request->input("old_password"), $user->password)) {
            throw new PasswordNotValidException;
        }

        $this->request->has('name') && $user->name = $this->request->input("name");
        $user->email = $this->request->input("email");
        $user->password = \Hash::make($this->request->input("password"));
        return $user->save();
    }

    /**
     * @return boolean
     *
     * @throws LoginNotValidException
     */
    public function login()
    {
        $validator = \Validator::make($this->request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $this->request->only('email', 'password');

        if ($this->auth->attempt($credentials, $this->request->has('remember'))) {
            return true;
        }

        throw new LoginNotValidException;
    }

    /**
     * @return boolean
     */
    public function logout()
    {
        $this->auth->logout();

        return true;
    }
}

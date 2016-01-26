<?php namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $name = trans('auth.guest');
        $userType = self::TRANSLATION_TAG_GUEST_USER;

        /** @var \App\Models\User $user */
        if ($user = app('sentinel')->getUser()) {
            $name = $user->name;
            $userType = self::TRANSLATION_TAG_REGISTERED_USER;
        }

        return view('index', ['userType' => $userType, 'name' => $name]);
    }
}

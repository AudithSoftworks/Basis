<?php namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function getIndex()
    {
        $name = trans('auth.guest');
        $userType = self::TRANSLATION_TAG_GUEST_USER;

        if ($user = app('sentinel')->getUser()) {
            $name = $user->name;
            $userType = self::TRANSLATION_TAG_REGISTERED_USER;
        }

        return view('index', ['userType' => $userType, 'name' => $name]);
    }
}

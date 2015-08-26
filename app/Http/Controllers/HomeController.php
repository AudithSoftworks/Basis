<?php namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['getProfile']]);
    }

    public function getIndex()
    {
        $name = trans('auth.guest');
        $userType = self::TRANSLATION_TAG_GUEST_USER;

        if ($user = \Sentinel::getUser()) {
            $name = $user->name;
            $userType = self::TRANSLATION_TAG_REGISTERED_USER;
        }

        return view('index', ['userType' => $userType, 'name' => $name]);
    }

    public function getProfile()
    {
        return view('profile', ['userType' => self::TRANSLATION_TAG_REGISTERED_USER, 'name' => \Sentinel::getUser()->name]);
    }
}

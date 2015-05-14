<?php namespace App\Http\Controllers;

class HomeController extends Controller
{

    /**
     * Show the application dashboard to the user.
     *
     * @return \Response
     */
    public function getHome()
    {
        $name = "Guest";
        $link = '<a href="/auth/login">Login</a>';

        if (\Auth::check()) {
            $user = \Auth::user();
            $name = $user->name;
            $link = '<a href="/auth/logout">Logout</a>';
        }

        return 'Welcome, '.$name.'! '.$link;
    }
}

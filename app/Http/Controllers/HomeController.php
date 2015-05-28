<?php namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function getIndex()
    {
        return redirect('admin-demo/index');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return \Response
     */
    public function getHome()
    {
        $name = "Guest";
        $link = '<a href="/login">Login</a>';

        if (\Auth::check()) {
            $user = \Auth::user();
            $name = $user->name;
            $link = '<a href="/logout">Logout</a>';
        }

        return 'Welcome, '.$name.'! '.$link;
    }
}

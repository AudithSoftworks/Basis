<?php namespace App\Http\Controllers;

use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function getIndex()
    {
        return redirect('/home');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function getHome()
    {
        $name = "Guest";

        if (\Auth::check()) {
            $user = \Auth::user();
            $name = $user->name;
        }

        return view('home', ['name' => $name]);
    }
}

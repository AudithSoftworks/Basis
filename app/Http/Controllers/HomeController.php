<?php namespace App\Http\Controllers;

class HomeController extends Controller
{

    /**
     * Show the application dashboard to the user.
     *
     * @return \Response
     */
    public function index()
    {
        redirect('admin-demo/dashboard');
    }
}

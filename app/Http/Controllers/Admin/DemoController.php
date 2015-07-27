<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DemoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function anyIndex()
    {
        return redirect('/admin-demo/dashboard');
    }

    public function anyDashboard()
    {
        return view('/admin-demo/dashboard');
    }

    public function getNotifications()
    {
        return view('/admin-demo/notifications');
    }
}

<?php namespace App\Http\Controllers;

class AdminController extends Controller
{
    /**
     * @return \Response
     */
    public function getIndex()
    {
        return view('admin.index');
    }
}

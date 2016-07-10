<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin/users');
    }
}

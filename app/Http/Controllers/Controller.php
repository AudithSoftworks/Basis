<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    const TRANSLATION_TAG_GUEST_USER = 0;

    const TRANSLATION_TAG_REGISTERED_USER = 1;

    const TRANSLATION_TAG_MALE = -2;

    const TRANSLATION_TAG_FEMALE = -1;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

<?php namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|RedirectResponse
     */
    public function index(Request $request)
    {
        if (app('translator')->getLocale() == config('app.locale') && app('translator')->getLocale() != $request->segment(1)) {
            return redirect(app('translator')->getLocale());
        }

        $name = trans('auth.guest');
        $userType = self::TRANSLATION_TAG_GUEST_USER;

        /** @var \App\Models\User $user */
        if ($user = app('auth.driver')->user()) {
            $name = $user->name;
            $userType = self::TRANSLATION_TAG_REGISTERED_USER;
        }

        return view('index', ['userType' => $userType, 'name' => $name]);
    }
}

<?php namespace App\Http\Middleware;

use App\Exceptions\Users\UserAlreadyLoggedInException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return \Closure
     * @throws \App\Exceptions\Users\UserAlreadyLoggedInException
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!app('auth.driver')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                throw new UserAlreadyLoggedInException;
            }

            abort(500, 'You already have logged in');
        }

        return $next($request);
    }
}

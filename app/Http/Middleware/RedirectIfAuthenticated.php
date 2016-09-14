<?php namespace App\Http\Middleware;

use App\Exceptions\Users\UserAlreadyLoggedInException;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return \Closure|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\Users\UserAlreadyLoggedInException
     */
    public function handle(Request $request, \Closure $next)
    {
        if (app('auth.driver')->check()) {
            if ($request->expectsJson()) {
                throw new UserAlreadyLoggedInException;
            }

            return redirect('/');
        }

        return $next($request);
    }
}

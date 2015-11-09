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
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!app('sentinel')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                throw new UserAlreadyLoggedInException;
            }

            return abort(500, 'You already have logged in');
        }

        return $next($request);
    }
}

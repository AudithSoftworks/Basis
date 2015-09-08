<?php namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
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
        if (!\Sentinel::guest()) {
            return new RedirectResponse(url('/'));
        }

        return $next($request);
    }
}

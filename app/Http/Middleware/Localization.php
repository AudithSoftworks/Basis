<?php namespace App\Http\Middleware;

use Illuminate\Http\Request;

class Localization
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
        app('translator')->setLocale($request->segment(1));

        return $next($request);
    }
}

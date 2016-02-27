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
        if (empty($locale = $request->segment(1)) || !array_key_exists($locale, config('app.locales'))) {
            $locale = config('app.fallback_locale');
        }
        app('translator')->setLocale($locale);

        return $next($request);
    }
}

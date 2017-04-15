<?php namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use \Illuminate\Auth\Middleware\Authenticate as IlluminateAuthenticateMiddleware;

class Authenticate extends IlluminateAuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string[]                 ...$guards
     *
     * @return mixed
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        try {
            $this->authenticate($guards);
        } catch (AuthenticationException $e) {
            if ($request->expectsJson()) {
                throw $e;
            } else {
                return redirect()->guest(route('login'));
            }
        }

        return $next($request);
    }
}

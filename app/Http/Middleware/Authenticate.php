<?php namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if (app('sentinel')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                throw new UnauthorizedHttpException('Unauthorized');
            } else {
                return redirect()->guest(route('login'));
            }
        }

        return $next($request);
    }
}

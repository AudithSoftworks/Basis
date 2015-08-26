<?php namespace App\Exceptions;

use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     *
     * @return void
     */
    public function report(\Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $exceptionClass = get_class($e);

            //---------------------------------------------------------------------------------------
            // Since we have custom ValidationException class, only validation-related exceptions
            // should be listed here. Laravel handles exceptions correctly, status-code wise.
            //---------------------------------------------------------------------------------------

            $response = response()->json(['exception' => $exceptionClass, 'message' => $e->getMessage()]);
            switch ($exceptionClass) {
                case Common\ValidationException::class:
                case Users\LoginNotValidException::class:
                case Users\PasswordNotValidException::class:
                case Users\TokenNotValidException::class:
                case HttpResponseException::class:
                case TokenMismatchException::class:
                    return $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                case UnauthorizedHttpException::class:
                    return $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                case NotActivatedException::class:
                    return $response->setStatusCode(Response::HTTP_FORBIDDEN);
                case NotFoundHttpException::class:
                    return $response->setStatusCode(Response::HTTP_NOT_FOUND);
                default:
                    $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();
                    if (!empty($statusCode)) {
                        $response->setStatusCode($statusCode);
                    } else {
                        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    return $response;
            }
        }

        $redirect = redirect()->back();
        if ($redirect->getTargetUrl() === config('app.url') || false === strpos($redirect->getTargetUrl(), config('app.url'))) {
            $redirect = redirect()->refresh();
        }

        return $redirect->withInput($request->all())->withErrors($e->getMessage());
    }
}

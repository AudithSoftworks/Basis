<?php namespace App\Exceptions;

use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response as IlluminateResponse;
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
            $responseBody = ['exception' => $exceptionClass, 'message' => $e->getMessage()];

            # General exceptions
            if ($e instanceof UnauthorizedHttpException) {
                $statusCode = IlluminateResponse::HTTP_UNAUTHORIZED;
            } elseif ($e instanceof NotActivatedException) {
                $statusCode = IlluminateResponse::HTTP_FORBIDDEN;
            } elseif ($e instanceof NotFoundHttpException) {
                $statusCode = IlluminateResponse::HTTP_NOT_FOUND;
            } elseif ($e instanceof \BadMethodCallException) {
                $statusCode = IlluminateResponse::HTTP_METHOD_NOT_ALLOWED;
            } elseif ($e instanceof \UnexpectedValueException || $e instanceof TokenMismatchException) {
                $statusCode = IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY;
            } else {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();
            }

            if (empty($statusCode)) {
                $statusCode = IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR;
            }

            return response()->json($responseBody)->setStatusCode($statusCode);
        }

        if ($request->method() != 'GET' && $request->header('content-type') == 'application/x-www-form-urlencoded') {
            return redirect()->back()->withInput($request->all())->withErrors($e->getMessage());
        }

        return parent::render($request, $e);
    }
}

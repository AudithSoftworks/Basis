<?php namespace App\Exceptions;

use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exception\HttpResponseException;
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
     * @param  \Exception $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        //---------------------------------------------------------------------------------------
        // Since we have custom ValidationException class, only validation-related exceptions
        // should be listed here. Laravel handles exceptions correctly, status-code wise.
        //---------------------------------------------------------------------------------------

        if ($request->ajax() || $request->wantsJson()) {
            $exceptionClass = get_class($e);
            $response = response()->json(['exception' => $exceptionClass, 'message' => $e->getMessage()]);
            switch ($exceptionClass) {
                case UnauthorizedHttpException::class:
                    $statusCode = IlluminateResponse::HTTP_UNAUTHORIZED;
                    break;
                case NotActivatedException::class:
                    $statusCode = IlluminateResponse::HTTP_FORBIDDEN;
                    break;
                case NotFoundHttpException::class:
                    $statusCode = IlluminateResponse::HTTP_NOT_FOUND;
                    break;
                case Common\NotImplementedException::class:
                    $statusCode = IlluminateResponse::HTTP_METHOD_NOT_ALLOWED;
                    break;
                case Common\ValidationException::class:
                case Users\LoginNotValidException::class:
                case Users\PasswordNotValidException::class:
                case Users\TokenNotValidException::class:
                case HttpResponseException::class:
                case TokenMismatchException::class:
                    $statusCode = IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY;
                    break;
                default:
                    $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();
                    break;
            }
            if (empty($statusCode)) {
                $statusCode = IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR;
            }

            return $response->setStatusCode($statusCode);
        }

        if ($request->method() != 'GET' && $request->header('content-type') == 'application/x-www-form-urlencoded') {
            return redirect()->back()->withInput($request->all())->withErrors($e->getMessage());
        }

        return parent::render($request, $e);
    }
}

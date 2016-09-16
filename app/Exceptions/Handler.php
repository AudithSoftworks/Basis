<?php namespace App\Exceptions;

use App\Exceptions\Users\UserNotActivatedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
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
        AuthorizationException::class,
        HttpException::class,
        IlluminateValidationException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
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
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function render($request, \Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        if ($request->expectsJson()) {
            $exceptionClass = get_class($e);
            $responseBody = ['exception' => $exceptionClass, 'message' => $e->getMessage()];

            # FileStream exceptions response body
            $preventRetry = true;
            $resetUpload = false;
            if ($e instanceof FileStream\UploadFilenameIsEmptyException) {
                $preventRetry = false;
            } elseif ($e instanceof FileStream\UploadIncompleteException) {
                $preventRetry = false;
                $resetUpload = true;
            } elseif ($e instanceof FileStream\UploadAttemptFailedException) {
                $preventRetry = false;
            }
            if (strpos($exceptionClass, 'App\Exceptions\FileStream') !== false) {
                $responseBody = ['exception' => $exceptionClass, 'error' => $e->getMessage(), 'preventRetry' => $preventRetry, 'reset' => $resetUpload];
            }

            # Status codes
            if ($e instanceof UnauthorizedHttpException) {
                $statusCode = IlluminateResponse::HTTP_UNAUTHORIZED;
            } elseif ($e instanceof UserNotActivatedException) {
                $statusCode = IlluminateResponse::HTTP_FORBIDDEN;
            } elseif ($e instanceof NotFoundHttpException) {
                $statusCode = IlluminateResponse::HTTP_NOT_FOUND;
            } elseif ($e instanceof \BadMethodCallException) {
                $statusCode = IlluminateResponse::HTTP_METHOD_NOT_ALLOWED;
            } elseif ($e instanceof \UnexpectedValueException || $e instanceof IlluminateValidationException || $e instanceof TokenMismatchException) {
                $statusCode = IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY;
                if ($e instanceof IlluminateValidationException) {
                    $messageBag = $e->validator->errors();
                    $responseBody['message'] = $messageBag->getMessages();
                }
            } elseif ($e instanceof \OverflowException) {
                $statusCode = IlluminateResponse::HTTP_REQUEST_ENTITY_TOO_LARGE;
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

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request                 $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}

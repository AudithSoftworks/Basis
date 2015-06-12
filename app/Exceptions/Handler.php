<?php namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exception\HttpResponseException;
use Psr\Log\LoggerInterface;
use Monolog\Handler\HipChatHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{


    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class
    ];

    /**
     * Create a new exception handler instance.
     *
     * @param  LoggerInterface $log
     */
    public function __construct(LoggerInterface $log)
    {
        $hipchatConfig = \Config::get('services.hipchat');
        $hipchatHandler = new HipChatHandler(
            $hipchatConfig['token'],
            $hipchatConfig['room'],
            $hipchatConfig['name'],
            false,
            $hipchatConfig['level']
        );

        \Log::getMonolog()->pushHandler($hipchatHandler);

        parent::__construct($log);
    }

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, \Exception $e)
    {
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
                    return $response->setStatusCode(422);
                default:
                    $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();
                    if (!empty($statusCode)) {
                        $response->setStatusCode($statusCode);
                    }

                    return $response;
            }
        }

        $redirect = redirect()->back();
        if ($redirect->getTargetUrl() === \Config::get('app.url')) {
            $redirect = redirect()->refresh();
        }

        return $redirect->withInput($request->all())->withErrors($e->getMessage());
    }
}

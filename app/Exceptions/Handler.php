<?php

namespace App\Exceptions;

use App\Contracts\ErrorApiResponse;
use App\Tools;
use Dotenv\Exception\ValidationException as ExceptionValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array<string>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        try {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            if ($exception instanceof HttpResponseException) {
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            } elseif ($exception instanceof MethodNotAllowedHttpException) {
                $status = Response::HTTP_METHOD_NOT_ALLOWED;
            } elseif ($exception instanceof UnauthorizedException) {
                $status = Response::HTTP_UNAUTHORIZED;
            } elseif ($exception instanceof NotFoundHttpException) {
                $status = Response::HTTP_NOT_FOUND;
            } elseif (
                $exception instanceof AuthorizationException
                || $exception instanceof AuthenticationException
            ) {
                $status = Response::HTTP_FORBIDDEN;
            } elseif ($exception instanceof ExceptionValidationException) {
                $status = Response::HTTP_BAD_REQUEST;
            }
            if ($status) Tools::teamsAlert(
                sprintf(
                    'Server (%s) error (%s): %s %s',
                    env('APP_CLIENT', 'unknown'),
                    get_class($exception),
                    $exception->getMessage(),
                    json_encode(Arr::except($request->all(), ['password']))
                )
            );
            return new ErrorApiResponse($exception, $status);
        } catch (\Exception $exceptione) {
            return parent::render($request, $exceptione);
        }
    }
}

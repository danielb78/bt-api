<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
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
     * @return JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        $headers = method_exists($exception, 'getHeaders') ? $exception->getHeaders() : [];

        if ($exception instanceof ValidationException) {
            return response()->json(
                ['errors' => ['validations' => $exception->errors()]],
                Response::HTTP_BAD_REQUEST,
                $headers
            );
        }

        return response()->json(
            ['errors' => ['runtime' => 'Something went wrong. Please try again later.']],
            Response::HTTP_BAD_REQUEST,
            $headers
        );
    }
}

<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    /** 
     * Handle error handling part globally.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof CustomException) {
            return $exception->render();
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => false,
                'errors' => $exception->validator->errors(),
            ], 422);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthenticated.',
            ], 401);
        }
     
        return response()->json([
            'status' => false,
            'error' => 'An unexpected error occurred. Please try again later.',
        ], 500);
    }
}

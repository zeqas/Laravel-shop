<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

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
        // 處理 AuthenticationException 錯誤
        $this->renderable(function (ForbiddenException $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 403);
        });

        // 處理 AuthenticationException 錯誤
        $this->renderable(function (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'msg' => '無效Token',
            ], 401);
        });

        // 處理 Validation 錯誤
        $this->renderable(function (ValidationException $e) {
            $errorMessageData = $e->validator->getMessageBag();

            return response()->json([
                'success' => false,
                'data' => $errorMessageData->getMessages(),
            ], 400);
        });

        // 處理其他 Error
        $this->renderable(function (Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => '未知錯誤',
                'error' => $e->getMessage(),
            ], 500);
        });
    }
}

<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// User routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Product routes
Route::get('products', [ProductController::class, 'index']);

// 登入後才能新增、修改、刪除
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [UserController::class, 'me']);

    Route::middleware(['role.check:admin'])->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy']);
    });

    // 將商品放入購物車
    Route::middleware(['role.check:customer'])->group(function () {
        // TODO: 使用者只能進入自己的購物車
        Route::get('cart', [CartController::class, 'show']);
        // 新增商品到購物車
        Route::post('cart', [CartController::class, 'store']);

        Route::put('cart/{cartProduct}', [CartController::class, 'update']);
        Route::delete('cart/clear', [CartController::class, 'clear']);
        Route::delete('cart/{product}', [CartController::class, 'destroy']);
        Route::post('cart/checkout', [CartController::class, 'checkout']);

        Route::get('orders', [OrderController::class, 'index']);
    });
});

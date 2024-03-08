<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Product routes

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('/products', [ProductController::class, "index"])->name('products.index');

Route::get('/products/create', [ProductController::class, "create"])->name('products.create');

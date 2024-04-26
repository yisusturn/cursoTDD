<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

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
Route::post('auth/login', [AuthController::class, 'login'])->name('api.login');
Route::post('auth/register', [AuthController::class, 'register'])->name('api.register');
Route::get('categories', [CategoryController::class, 'index'])->name('api.categories.index');
Route::get('categories/{category}', [CategoryController::class, 'show'])->name('api.category.show');

Route::get('products', [ProductController::class, 'index'])->name('api.products.index');
Route::get('products/{product}', [ProductController::class, 'show'])->name('api.product.show');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('auth/me', [AuthController::class, 'me'])->name('api.auth.me');
    Route::get('auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

    Route::group(['middleware' => 'admin'], function () {
        Route::post('category/store', [CategoryController::class, 'store'])->name('api.category.store');
        Route::put('category/update/{category}', [CategoryController::class, 'update'])->name('api.category.update');
        Route::delete('category/delete/{category}', [CategoryController::class, 'delete'])->name('api.category.delete');

        Route::post('products/store', [ProductController::class, 'store'])->name('api.products.store');
    });
});
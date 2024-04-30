<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
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
    Route::post('cart/store', [CartController::class, 'store'])->name('api.cart.store');
    Route::put('cart/update/{cart}', [CartController::class, 'update'])->name('api.cart.update');
    Route::delete('cart/delete/{cart}', [CartController::class, 'delete'])->name('api.cart.delete');
    Route::get('cart', [CartController::class, 'index'])->name('api.cart.index');
    Route::get('orders', [OrderController::class, 'orders'])->name('api.orders.index');
    Route::get('orders/user/', [OrderController::class, 'getByUser'])->name('api.orders.getByUser');
    Route::post('orders/store', [OrderController::class, 'store'])->name('api.orders.store');

    Route::group(['middleware' => 'admin'], function () {
        Route::post('category/store', [CategoryController::class, 'store'])->name('api.category.store');
        Route::put('category/update/{category}', [CategoryController::class, 'update'])->name('api.category.update');
        Route::delete('category/delete/{category}', [CategoryController::class, 'delete'])->name('api.category.delete');

        Route::post('products/store', [ProductController::class, 'store'])->name('api.products.store');
        Route::put('products/update/{product}', [ProductController::class, 'update'])->name('api.products.update');
        Route::delete('products/delete/{product}', [ProductController::class, 'delete'])->name('api.products.delete');
    });
});
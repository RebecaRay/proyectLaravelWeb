<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Middleware\AdminMiddleware;


// Public
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::get('/product_index', [ProductController::class, 'index']);
Route::get('/category', [CategoryController::class, 'showCategories']);
Route::get('/category/{categoryId}', [CategoryController::class, 'showProductsByCategory']);

// Routes accessible to authenticated users
Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('logout', [RegisterController::class, 'logout']);
    Route::get('/user/carts', [CartController::class, 'getUserCarts']);
    Route::post('/cart', [CartController::class, 'store']);

    // Routes accessible only to administrators
    Route::middleware(['admin'])->group(function () {
        //Products
        Route::get('products/all', [ProductController::class, 'getAll']);
        Route::post('products/create', [ProductController::class, 'create']);
        Route::get('products/id/{productid}', [ProductController::class, 'byId']);
        Route::post('products/update', [ProductController::class, 'update']);
        Route::delete('products/id/delete/{productid}', [ProductController::class, 'destroy']);

        //Categories
        Route::get('categories/all', [CategoryController::class, 'getAll']);
        Route::post('categories/create', [CategoryController::class, 'create']);
        Route::get('categories/id/{categoryid}', [CategoryController::class, 'byId']);
        Route::post('categories/update', [CategoryController::class, 'update']);
        Route::delete('categories/id/delete/{categoryid}', [CategoryController::class, 'destroy']);

        //Suppliers
        Route::get('suppliers/all', [SupplierController::class, 'getAll']);
        Route::post('suppliers/create', [SupplierController::class, 'create']);
        Route::get('suppliers/id/{supplierid}', [SupplierController::class, 'byId']);
        Route::post('suppliers/update', [SupplierController::class, 'update']);
        Route::delete('suppliers/id/delete/{supplierid}', [SupplierController::class, 'destroy']);

        //Users
        Route::get('users/all', [UserController::class, 'getAll']);
        Route::post('users/create', [UserController::class, 'create']);
        Route::get('users/id/{userId}', [UserController::class, 'byId']);
        Route::post('users/update', [UserController::class, 'update']);
        Route::delete('users/id/delete/{userid}', [UserController::class, 'destroy']);

        //Orders
        Route::get('carts/all', [CartController::class, 'index']);
    });
});

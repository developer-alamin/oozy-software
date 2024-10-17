<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductModelController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\SupplierController;

Route::resource('suppliers', SupplierController::class);
Route::resource('models', ProductModelController::class);

// Admin Auth Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('auth:admin');
});

// User Auth Routes
Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:user');
    Route::get('/dashboard', [UserController::class, 'dashboard'])->middleware('auth:user');
});

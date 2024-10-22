<?php

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\ProductModelController;

Route::resource('suppliers', SupplierController::class);
Route::resource('models', ProductModelController::class);
Route::resource('category', CategoryController::class);

// brand all 
Route::get('/brand/trashed', [BrandController::class, 'trashed']);
Route::post('/brand/{id}/restore', [BrandController::class, 'restore']);
Route::delete('/brand/{id}/force-delete', [BrandController::class, 'forceDelete']);
Route::get('/brand/trashed-count', [BrandController::class, 'trashedBrandsCount']);
Route::resource('brand', BrandController::class);
// units
Route::resource('units', UnitController::class);
// 


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
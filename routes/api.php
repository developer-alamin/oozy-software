<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\TechnicianController;


use App\Http\Controllers\Admin\LineController;
use App\Http\Controllers\Admin\GroupController;


use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\ProductModelController;
use App\Http\Controllers\Auth\AuthController;

Route::resource('suppliers', SupplierController::class);
Route::resource('models', ProductModelController::class);
Route::resource('category', CategoryController::class);



Route::resource('line', LineController::class);
Route::resource('group', GroupController::class);

// brand
Route::get('/brand/trashed', [BrandController::class, 'trashed']);
Route::post('/brand/{id}/restore', [BrandController::class, 'restore']);
Route::delete('/brand/{id}/force-delete', [BrandController::class, 'forceDelete']);
Route::get('/brand/trashed-count', [BrandController::class, 'trashedBrandsCount']);
Route::resource('brand', BrandController::class);
// units
Route::resource('units', UnitController::class);
// Technician
Route::get('/technician/trashed', [TechnicianController::class, 'trashed']);
Route::post('/technician/{id}/restore', [TechnicianController::class, 'restore']);
Route::delete('/technician/{id}/force-delete', [TechnicianController::class, 'forceDelete']);
Route::get('/technician/trashed-count', [TechnicianController::class, 'trashedTechniciansCount']);
Route::resource('technician', TechnicianController::class);
// Rent
Route::resource('rent', RentController::class);
// Company
Route::resource('company', CompanyController::class);
// Factory
Route::resource('factory', FactoryController::class);




// Line Group Controllers start form here
Route::controller(LineController::class)
->prefix('lines')
->as("lines")
->group(function () {
    Route::get('/trashed-count', 'lineTrashedCount')->name('line.Trashed.Count');
    Route::get('/trashed', 'lineTrashed')->name('line.Trashed');
    Route::post('{id}/restore', 'lineRestore')->name('line.Restore');
    Route::delete('{id}/forceDelete', 'lineforceDelete')->name('line.lineforce.Delete');
});
// Line Group Controllers end form here


// Groups Gorup Controller start form here
Route::controller(GroupController::class)
->prefix("groups")
->as("groups")
->group(function () {
    Route::get('/trashed-count', 'groupsTrashedCount')->name('groups.Trashed.Count');
    Route::get('/trashed', 'groupsTrashed')->name('groups.Trashed');
    Route::post('{id}/restore', 'groupsRestore')->name('groups.Restore');
    Route::delete('{id}/forceDelete', 'groupsforceDelete')->name('groups.groupsforce.Delete');
});
// Groups Gorup Controller end form here













// Admin Auth Routes
Route::prefix('admin')->group(function () {
    // admin user 
    Route::get('/user/all', [AuthController::class, 'fetchAdminAllUserInfo']);
    Route::post('/user/store', [AuthController::class, 'adminUserCreate']);
    Route::get('/user/edit/{id}', [AuthController::class, 'adminUserEdit']);
    Route::get('/user/trash', [AuthController::class, 'fetchAdminAllUserTrashInfo']);

    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('auth:admin');
});

Route::get('/user/role/auth', [AuthController::class, 'fetchUserAuthRoleInfo']);
// User Auth Routes
Route::prefix('user')->group(function () {

    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:user');
    Route::get('/dashboard', [UserController::class, 'dashboard'])->middleware('auth:user');
});

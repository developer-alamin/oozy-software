<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RentController;
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
// models
Route::get('/models/trashed', [ProductModelController::class, 'trashed']);
Route::post('/models/{id}/restore', [ProductModelController::class, 'restore']);
Route::delete('/models/{id}/force-delete', [ProductModelController::class, 'forceDelete']);
Route::get('/models/trashed-count', [ProductModelController::class, 'trashedModelsCount']);
Route::resource('models', ProductModelController::class);
// category
Route::get('/category/trashed', [CategoryController::class, 'trashed']);
Route::post('/category/{id}/restore', [CategoryController::class, 'restore']);
Route::delete('/category/{id}/force-delete', [CategoryController::class, 'forceDelete']);
Route::get('/category/trashed-count', [CategoryController::class, 'trashedcategorysCount']);
Route::resource('category', CategoryController::class);



Route::resource('line', LineController::class);

Route::resource('group', GroupController::class);

// Rent
Route::resource('rent', RentController::class);

// brand
Route::get('/brand/trashed', [BrandController::class, 'trashed']);
Route::post('/brand/{id}/restore', [BrandController::class, 'restore']);
Route::delete('/brand/{id}/force-delete', [BrandController::class, 'forceDelete']);
Route::get('/brand/trashed-count', [BrandController::class, 'trashedBrandsCount']);
Route::resource('brand', BrandController::class);
// units
Route::get('/units/trashed', [UnitController::class, 'trashed']);
Route::post('/units/{id}/restore', [UnitController::class, 'restore']);
Route::delete('/units/{id}/force-delete', [UnitController::class, 'forceDelete']);
Route::get('/units/trashed-count', [UnitController::class, 'trashedUnitsCount']);
Route::resource('units', UnitController::class);
// Technician
Route::get('/technician/trashed', [TechnicianController::class, 'trashed']);
Route::post('/technician/{id}/restore', [TechnicianController::class, 'restore']);
Route::delete('/technician/{id}/force-delete', [TechnicianController::class, 'forceDelete']);
Route::get('/technician/trashed-count', [TechnicianController::class, 'trashedTechniciansCount']);
Route::resource('technician', TechnicianController::class);

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
Route::get('get-technician', [GroupController::class, 'getTechnician']);
// Groups Gorup Controller end form here


// Group Rents Controller start form here
Route::controller(RentController::class)
->prefix('rents')
->as("rents")
->group(function () {
     Route::get('/trashed-count', 'rentstrashedcount')->name('rents.trashed.count');
     Route::get('/trashed', 'rentstrashed')->name('rents.trashed');
     Route::post('{id}/restore', 'rentsrestore')->name('line.restore');
     Route::delete('{id}/force-delete', 'rentsforcedelete')->name('rents.force.delete');
});
// Group Rents Controller End form here

// Admin Auth Routes
Route::prefix('admin')->group(function () {
    // admin user
    Route::get('/user/all', [AuthController::class, 'fetchAdminAllUserInfo']);
    Route::post('/user/store', [AuthController::class, 'adminUserCreate']);
    Route::get('/user/edit/{id}', [AuthController::class, 'adminUserEdit']);
    Route::get('/user/trash', [AuthController::class, 'fetchAdminAllUserTrashInfo']);

    Route::get('/company/user/all', [AuthController::class, 'allUserInfo']);
    Route::post('/company/user/store', [AuthController::class, 'userCreate']);

    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('auth:admin');
});
Route::get('/auth/user', [AuthController::class, 'fetchGobalUserAuthInfo']);
Route::get('/user/role/auth', [AuthController::class, 'fetchUserAuthRoleInfo']);
// User Auth Routes
Route::prefix('user')->group(function () {

    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:user');
    Route::get('/dashboard', [UserController::class, 'dashboard'])->middleware('auth:user');
});

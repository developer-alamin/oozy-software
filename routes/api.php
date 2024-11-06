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
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\Mechine\TypeController;
use App\Http\Controllers\Admin\SourceController;


use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\ProductModelController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OperatorController;

Route::resource('suppliers', SupplierController::class);
// suppliers Group Controllers start form here
Route::controller(SupplierController::class)
->prefix('supplier')
->as("supplier")
->group(function () {
    Route::get('/trashed-count', 'suppliertrashedcount')->name('supplier.trashed.count');
    Route::get('/trashed', 'suppliertrashed')->name('supplier.trashed');
    Route::post('{id}/restore', 'supplierrestore')->name('line.Restore');
    Route::delete('{id}/force-delete', 'supplierforcedelete')->name('supplier.force.delete');
});

// floor group route declare start form here
Route::get('/floor/{uuid}/edit', [FloorController::class, 'edit'])->name('floor.edit');
Route::put('/floor/{uuid}', [FloorController::class, 'update'])->name('floor.update');
Route::controller(FloorController::class)
->prefix("floors")
->as("floors")
->group(function(){
    Route::get('/trashed-count', 'floortrashedcount')->name('floor.trashed.count');
    Route::get('/trashed', 'floortrashed')->name('floor.trashed');
    Route::post('{id}/restore', 'floorrestore')->name('floor.restore');
    Route::delete('{id}/force-delete', 'floorforcedelete')->name('floor.force.delete');
});
Route::resource('floor', FloorController::class);

// floor group route declare end form here

//Mechine Group Route declare start form here

Route::prefix("/mechine")->group(function(){
    Route::get('/type/{uuid}/edit', [TypeController::class, 'edit'])->name('type.edit');
    Route::put('/type/{uuid}', [TypeController::class, 'update'])->name('type.update');
    Route::resource('type', TypeController::class);
    Route::controller(TypeController::class)
    ->prefix('types')
    ->as("types")
    ->group(function () {
         Route::get('/trashed-count', 'typestrashedcount')->name('types.trashed.count');
         Route::get('/trashed', 'typestrashed')->name('types.trashed');
         Route::post('{id}/restore', 'typesrestore')->name('types.restore');
         Route::delete('{id}/force-delete', 'typesforcedelete')->name('types.force.delete');
    });
    Route::resource('source', SourceController::class);
    Route::controller(SourceController::class)
    ->prefix('sources')
    ->as("sources")
    ->group(function () {
         Route::get('/trashed-count', 'sourcestrashedcount')->name('sources.trashed.count');
         Route::get('/trashed', 'sourcestrashed')->name('sources.trashed');
         Route::post('{id}/restore', 'sourcesrestore')->name('source.restore');
         Route::delete('{id}/force-delete', 'sourcesforcedelete')->name('sources.force.delete');
    });

});
//Mechine Group Route declare end form here


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


// Line Route
Route::get('/line/{uuid}/edit', [LineController::class, 'edit'])->name('lines.edit');
Route::put('/line/{uuid}', [LineController::class, 'update'])->name('lines.update');
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
Route::resource('line', LineController::class);

// Groups Route
Route::get('/get_users', [GroupController::class, 'getUsers']);
Route::get('/group/{uuid}/edit', [GroupController::class, 'edit'])->name('group.edit');
Route::put('/group/{uuid}', [GroupController::class, 'update'])->name('group.update');
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
Route::get('/technician/{uuid}/edit', [TechnicianController::class, 'edit'])->name('technician.edit');
Route::put('/technician/{uuid}', [TechnicianController::class, 'update'])->name('technician.update');
Route::resource('technician', TechnicianController::class);
// operator
Route::get('/operator/trashed', [OperatorController::class, 'trashed']);
Route::post('/operator/{id}/restore', [OperatorController::class, 'restore']);
Route::delete('/operator/{id}/force-delete', [OperatorController::class, 'forceDelete']);
Route::get('/operator/trashed-count', [OperatorController::class, 'trashedOperatorsCount']);
Route::get('/operator/{uuid}/edit', [OperatorController::class, 'edit'])->name('operator.edit');
Route::put('/operator/{uuid}', [OperatorController::class, 'update'])->name('operator.update');
Route::resource('operator', OperatorController::class);


// Company
Route::resource('company', CompanyController::class);
// Factory
Route::get('/get_companys', [FactoryController::class, 'getCompanys']);
Route::get('/get_floors', [FactoryController::class, 'getFloors']);
Route::get('/get_units', [FactoryController::class, 'getUnits']);
Route::get('/get_lines', [FactoryController::class, 'getLines']);
Route::get('/factory/edit/{uuid}', [FactoryController::class, 'edit'])->name('factory.edit');
Route::put('/factory/{uuid}', [FactoryController::class, 'update'])->name('factory.update');
Route::resource('factory', FactoryController::class);





// Group Rents Controller start form here
Route::controller(RentController::class)
->prefix('rents')
->as("rents")
->group(function () {
     Route::get('/trashed-count', 'rentstrashedcount')->name('rents.trashed.count');
     Route::get('/trashed', 'rentstrashed')->name('rents.trashed');
     Route::post('{id}/restore', 'rentsrestore')->name('rents.restore');
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

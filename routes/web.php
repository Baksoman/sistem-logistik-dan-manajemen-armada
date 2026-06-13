<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DriverProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RolePermissionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::middleware('permission:manage_users')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::post('/', [UserController::class, 'store'])->name('users.store');
            Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
        
        Route::prefix('rbac')->group(function () {
            Route::get('/', [RolePermissionController::class, 'index'])->name('rbac.index');
            Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('rbac.roles.store');
            Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('rbac.permissions.store');
            Route::put('/{role}', [RolePermissionController::class, 'update'])->name('rbac.update');
        });
    });

    Route::middleware('permission:manage_drivers')->group(function () {
        Route::prefix('drivers')->group(function () {
            Route::get('/', [DriverProfileController::class, 'index'])->name('drivers.index');
            Route::post('/', [DriverProfileController::class, 'store'])->name('drivers.store');
            Route::put('/{driver}', [DriverProfileController::class, 'update'])->name('drivers.update');
            Route::delete('/{driver}', [DriverProfileController::class, 'destroy'])->name('drivers.destroy');
        });
    });
    
    Route::middleware('permission:manage_vehicles')->group(function () {
        Route::prefix('fleet')->group(function () {
            Route::get('/', [VehicleController::class, 'index'])->name('fleet.index');
            Route::post('/', [VehicleController::class, 'store'])->name('fleet.store');
            Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('fleet.update');
            Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('fleet.destroy');
        });
    });
});
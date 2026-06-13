<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DriverProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\Logistik\RouteController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Warehouse\InventoryController;

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

            Route::get('/maintenances', [VehicleMaintenanceController::class, 'index'])->name('fleet.maintenances.index');
            Route::post('/maintenances', [VehicleMaintenanceController::class, 'store'])->name('fleet.maintenances.store');
            Route::put('/maintenances/{maintenance}', [VehicleMaintenanceController::class, 'update'])->name('fleet.maintenances.update');
        });
    });

    Route::middleware('permission:manage_inventory')->group(function () {
        Route::prefix('warehouse')->group(function () {
            Route::prefix('warehouses')->group(function () {
                Route::get('/', [WarehouseController::class, 'index'])->name('warehouse.warehouses.index');
                Route::post('/', [WarehouseController::class, 'store'])->name('warehouse.warehouses.store');
                Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('warehouse.warehouses.update');
                Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.warehouses.destroy');
            });

            Route::prefix('inventory')->group(function () {
                Route::get('/', [InventoryController::class, 'index'])->name('warehouse.inventory.index');
                Route::post('/', [InventoryController::class, 'store'])->name('warehouse.inventory.store');
                Route::put('/{inventory}', [InventoryController::class, 'update'])->name('warehouse.inventory.update');
                Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('warehouse.inventory.destroy');
            });
        });
    });

    Route::middleware('permission:manage_routes')->group(function () {
        Route::prefix('logistik/routes')->group(function () {
            Route::get('/', [RouteController::class, 'index'])->name('routes.index');
            Route::get('/create', [RouteController::class, 'create'])->name('routes.create');
            Route::post('/', [RouteController::class, 'store'])->name('routes.store');
            Route::get('/{route}', [RouteController::class, 'show'])->name('routes.show');
            Route::delete('/{route}', [RouteController::class, 'destroy'])->name('routes.destroy');
            Route::post('/calculate-preview', [RouteController::class, 'calculatePreview'])->name('routes.calculate-preview');
        });
    });
});
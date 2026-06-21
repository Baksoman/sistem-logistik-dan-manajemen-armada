<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Driver\WorkspaceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DriverProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\Logistik\RouteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TariffController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Warehouse\InventoryController;
use App\Http\Controllers\Warehouse\ZoneController;
use App\Http\Controllers\Warehouse\RackController;
use App\Http\Controllers\Warehouse\ItemCategoryController;
use App\Http\Controllers\Warehouse\InboundController;
use App\Http\Controllers\Warehouse\OutboundController;
use App\Http\Controllers\Warehouse\WarehouseDashboardController;
use App\Http\Controllers\Logistik\DashboardController as LogistikDashboardController;

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
            Route::get('/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');
            Route::get('/export/pdf', [UserController::class, 'exportPdf'])->name('users.export.pdf');
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
            Route::get('/export/excel', [DriverProfileController::class, 'exportExcel'])->name('drivers.export.excel');
            Route::get('/export/pdf', [DriverProfileController::class, 'exportPdf'])->name('drivers.export.pdf');
            Route::get('/', [DriverProfileController::class, 'index'])->name('drivers.index');
            Route::post('/', [DriverProfileController::class, 'store'])->name('drivers.store');
            Route::put('/{driver}', [DriverProfileController::class, 'update'])->name('drivers.update');
            Route::delete('/{driver}', [DriverProfileController::class, 'destroy'])->name('drivers.destroy');
        });
    });
    
    Route::middleware('permission:manage_vehicles')->group(function () {
        Route::prefix('fleet')->group(function () {
            Route::get('/export/excel', [VehicleController::class, 'exportExcel'])->name('fleet.export.excel');
            Route::get('/export/pdf', [VehicleController::class, 'exportPdf'])->name('fleet.export.pdf');
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
        Route::prefix('warehouse-panel')->group(function () {
            Route::get('/', [WarehouseDashboardController::class, 'index'])->name('warehouse.dashboard');
            
            Route::prefix('warehouses')->group(function () {
                Route::get('/export/excel', [WarehouseController::class, 'exportExcel'])->name('warehouse.warehouses.export.excel');
                Route::get('/export/pdf', [WarehouseController::class, 'exportPdf'])->name('warehouse.warehouses.export.pdf');
                Route::get('/', [WarehouseController::class, 'index'])->name('warehouse.warehouses.index');
                Route::post('/', [WarehouseController::class, 'store'])->name('warehouse.warehouses.store');
                Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('warehouse.warehouses.update');
                Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.warehouses.destroy');
            });

            Route::prefix('categories')->group(function () {
                Route::get('/', [ItemCategoryController::class, 'index'])->name('warehouse.categories.index');
                Route::post('/', [ItemCategoryController::class, 'store'])->name('warehouse.categories.store');
                Route::put('/{category}', [ItemCategoryController::class, 'update'])->name('warehouse.categories.update');
                Route::delete('/{category}', [ItemCategoryController::class, 'destroy'])->name('warehouse.categories.destroy');
            });

            Route::prefix('zones')->group(function () {
                Route::get('/', [ZoneController::class, 'index'])->name('warehouse.zones.index');
                Route::post('/', [ZoneController::class, 'store'])->name('warehouse.zones.store');
                Route::put('/{zone}', [ZoneController::class, 'update'])->name('warehouse.zones.update');
                Route::delete('/{zone}', [ZoneController::class, 'destroy'])->name('warehouse.zones.destroy');
            });

            Route::prefix('racks')->group(function () {
                Route::get('/', [RackController::class, 'index'])->name('warehouse.racks.index');
                Route::post('/', [RackController::class, 'store'])->name('warehouse.racks.store');
                Route::put('/{rack}', [RackController::class, 'update'])->name('warehouse.racks.update');
                Route::delete('/{rack}', [RackController::class, 'destroy'])->name('warehouse.racks.destroy');
            });

            Route::prefix('inventory')->group(function () {
                Route::get('/export/excel', [InventoryController::class, 'exportExcel'])->name('warehouse.inventory.export.excel');
                Route::get('/export/pdf', [InventoryController::class, 'exportPdf'])->name('warehouse.inventory.export.pdf');
                Route::get('/', [InventoryController::class, 'index'])->name('warehouse.inventory.index');
                Route::post('/', [InventoryController::class, 'store'])->name('warehouse.inventory.store');
                Route::put('/{inventory}', [InventoryController::class, 'update'])->name('warehouse.inventory.update');
                Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('warehouse.inventory.destroy');
            });

            Route::prefix('inbound')->group(function () {
                Route::get('/export/excel', [InboundController::class, 'exportExcel'])->name('warehouse.inbound.export.excel');
                Route::get('/export/pdf', [InboundController::class, 'exportPdf'])->name('warehouse.inbound.export.pdf');
                Route::get('/', [InboundController::class, 'index'])->name('warehouse.inbound.index');
                Route::post('/', [InboundController::class, 'store'])->name('warehouse.inbound.store');
            });

            Route::prefix('outbound')->group(function () {
                Route::get('/export/excel', [OutboundController::class, 'exportExcel'])->name('warehouse.outbound.export.excel');
                Route::get('/export/pdf', [OutboundController::class, 'exportPdf'])->name('warehouse.outbound.export.pdf');
                Route::get('/', [OutboundController::class, 'index'])->name('warehouse.outbound.index');
                Route::post('/', [OutboundController::class, 'store'])->name('warehouse.outbound.store');
            });
        });
    });

    Route::prefix('/logistik-panel')->group(function () {
        Route::get('/', [LogistikDashboardController::class, 'index'])->name('dashboard.logistik.index');
        
        Route::middleware('permission:manage_routes')->group(function () {
            Route::prefix('/routes')->group(function () {
                Route::get('/', [RouteController::class, 'index'])->name('routes.index');
                Route::get('/create', [RouteController::class, 'create'])->name('routes.create');
                Route::post('/', [RouteController::class, 'store'])->name('routes.store');
                Route::get('/{route}', [RouteController::class, 'show'])->name('routes.show');
                Route::delete('/{route}', [RouteController::class, 'destroy'])->name('routes.destroy');
                Route::post('/calculate-preview', [RouteController::class, 'calculatePreview'])->name('routes.calculate-preview');
            });
    
            Route::get('tariffs/export/excel', [TariffController::class, 'exportExcel'])->name('tariffs.export.excel');
            Route::get('tariffs/export/pdf', [TariffController::class, 'exportPdf'])->name('tariffs.export.pdf');
            Route::resource('tariffs', TariffController::class);
        });

        Route::middleware('permission:manage_orders')->group(function () {
            Route::prefix('orders')->group(function () {
                Route::get('/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');
                Route::get('/export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
                Route::get('/', [OrderController::class, 'index'])->name('orders.index');
                Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
                Route::post('/', [OrderController::class, 'store'])->name('orders.store');
                Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
                Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
                Route::put('/{order}', [OrderController::class, 'update'])->name('orders.update');
                Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
                Route::get('/warehouse-items/{warehouse}', [OrderController::class, 'getWarehouseItems'])->name('orders.warehouse-items');
            });
        });
    
        Route::middleware('permission:manage_shipments')->group(function () {
            Route::prefix('shipments')->group(function () {
                Route::get('/export/excel', [ShipmentController::class, 'exportExcel'])->name('shipments.export.excel');
                Route::get('/export/pdf', [ShipmentController::class, 'exportPdf'])->name('shipments.export.pdf');
                Route::get('/', [ShipmentController::class, 'index'])->name('shipments.index');
                Route::get('/create', [ShipmentController::class, 'create'])->name('shipments.create');
                Route::post('/', [ShipmentController::class, 'store'])->name('shipments.store');
                Route::get('/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
                Route::post('/{shipment}/start', [ShipmentController::class, 'start'])->name('shipments.start');
                Route::post('/{shipment}/complete', [ShipmentController::class, 'complete'])->name('shipments.complete');
                Route::post('/{shipment}/unload', [ShipmentController::class, 'unload'])->name('shipments.unload');
            });
        });
    });

    // Driver PWA Routes
    Route::middleware('role:driver')->prefix('driver/workspace')->name('driver.')->group(function () {
        Route::get('/', [WorkspaceController::class, 'index'])->name('workspace.index');
        Route::get('/history', [WorkspaceController::class, 'history'])->name('workspace.history');
        Route::get('/history/shipments/{shipment}', [WorkspaceController::class, 'historyShow'])->name('workspace.history.show');
        Route::get('/costs', [WorkspaceController::class, 'globalCosts'])->name('workspace.costs.global');

        Route::prefix('shipments/{shipment}')->group(function () {
            Route::get('/', [WorkspaceController::class, 'show'])->name('workspace.show');
            Route::post('/start', [WorkspaceController::class, 'startJourney'])->name('shipments.start');
            Route::get('/packages', [WorkspaceController::class, 'packages'])->name('workspace.packages');
            Route::post('/packages/{order}/unload', [WorkspaceController::class, 'unloadPackage'])->name('workspace.unload');
            Route::get('/costs', [WorkspaceController::class, 'costs'])->name('workspace.costs');
            Route::post('/costs', [WorkspaceController::class, 'storeCost'])->name('workspace.costs.store');
        });
    });
});
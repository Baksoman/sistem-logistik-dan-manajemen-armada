<?php

use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\DriverSearchController;
use App\Http\Controllers\Api\LocationSearchController;
use App\Http\Controllers\Api\OrderSearchController;
use App\Http\Controllers\Api\RouteSearchController;
use App\Http\Controllers\Api\ShipmentSearchController;
use App\Http\Controllers\Api\UserSearchController;
use App\Http\Controllers\Api\VehicleSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/locations/search', [LocationSearchController::class, 'search']);

Route::middleware(['web', 'auth', 'role:driver'])->group(function () {
    Route::post('/driver/location/ping', [DriverController::class, 'pingLocation'])
        ->name('api.driver.location.ping');
});

// Endpoint untuk simulator (testing only)
Route::post('/simulator/location/ping', [DriverController::class, 'simulatorPing']);

Route::middleware(['web', 'auth'])->prefix('search')->name('api.search.')->group(function () {
    Route::get('/users',     UserSearchController::class)->middleware('permission:manage_users')->name('users');
    Route::get('/orders',    OrderSearchController::class)->middleware('permission:view_orders')->name('orders');
    Route::get('/shipments', ShipmentSearchController::class)->middleware('permission:manage_shipments')->name('shipments');
    Route::get('/routes',    RouteSearchController::class)->middleware('permission:manage_routes')->name('routes');
    Route::get('/vehicles',  VehicleSearchController::class)->middleware('permission:manage_vehicles')->name('vehicles');
    Route::get('/drivers',   DriverSearchController::class)->middleware('permission:manage_drivers')->name('drivers');
});


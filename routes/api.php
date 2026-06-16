<?php

use App\Http\Controllers\Api\DriverController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationSearchController;

Route::get('/locations/search', [LocationSearchController::class, 'search']);

Route::middleware(['web', 'auth', 'role:driver'])->group(function () {
    Route::post('/driver/location/ping', [DriverController::class, 'pingLocation'])->name('api.driver.location.ping');
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationSearchController;

Route::get('/locations/search', [LocationSearchController::class, 'search']);

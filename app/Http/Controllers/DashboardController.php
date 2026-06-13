<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Logistik\DashboardController as LogistikDashboard;
use App\Http\Controllers\Warehouse\DashboardController as WarehouseDashboard;
use App\Http\Controllers\Driver\DashboardController as DriverDashboard;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Check using Spatie Permission roles
        if ($user->hasRole('Super Admin')) {
            return app(AdminDashboard::class)->index();
        }

        if ($user->hasRole('Admin Logistik')) {
            return app(LogistikDashboard::class)->index();
        }

        if ($user->hasRole('Warehouse')) {
            return app(WarehouseDashboard::class)->index();
        }

        if ($user->hasRole('Driver')) {
            return app(DriverDashboard::class)->index();
        }

        return app(CustomerDashboard::class)->index();
    }
}

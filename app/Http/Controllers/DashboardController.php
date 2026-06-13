<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Check using Spatie Permission roles
        if ($user->hasRole('Super Admin')) {
            return app(AdminController::class)->index();
        }

        if ($user->hasRole('Admin Logistik')) {
            return app(LogistikController::class)->index();
        }

        if ($user->hasRole('Warehouse')) {
            return app(WarehouseController::class)->index();
        }

        if ($user->hasRole('Driver')) {
            return app(DriverController::class)->index();
        }

        // Default to Customer or generic dashboard
        return app(CustomerController::class)->index();
    }
}

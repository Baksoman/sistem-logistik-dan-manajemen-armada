<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Logistik\DashboardController as LogistikDashboard;
use App\Http\Controllers\Driver\DashboardController as DriverDashboard;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Driver\WorkspaceController;

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

        if ($user->hasRole('Staff Warehouse')) {
            return redirect()->route('warehouse.dashboard');
        }

        if ($user->hasRole('Driver')) {
            return redirect()->route('driver.workspace.index');
        }

        return app(CustomerDashboard::class)->index();
    }
}

<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.warehouse.index');
    }
}

<?php

namespace App\Http\Controllers\Logistik;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.logistik.index');
    }
}

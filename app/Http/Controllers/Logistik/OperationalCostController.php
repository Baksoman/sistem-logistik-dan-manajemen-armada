<?php

namespace App\Http\Controllers\Logistik;

use App\Http\Controllers\Controller;
use App\Models\CostCategory;
use App\Models\DriverProfile;
use App\Http\Requests\Search\OperationalCostSearchRequest;
use App\Http\Controllers\Api\OperationalCostSearchController;
use App\QueryFilters\OperationalCostFilter;

class OperationalCostController extends Controller
{
    public function index(OperationalCostSearchRequest $request, OperationalCostFilter $filter)
    {
        // Fetch initial data payload for Alpine x-data
        $apiController = new OperationalCostSearchController();
        $initialData = $apiController($request, $filter)->response()->getData(true);

        $categories = CostCategory::orderBy('name')->get();
        $drivers = DriverProfile::with('user')->get()->sortBy('user.name');

        return view('logistik.operational-costs.index', compact('initialData', 'categories', 'drivers'));
    }
}

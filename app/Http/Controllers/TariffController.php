<?php

namespace App\Http\Controllers;

use App\Models\Tariff;
use App\Models\Route;
use App\Models\VehicleType;
use App\Services\TariffService;
use Illuminate\Http\Request;
use Exception;

class TariffController extends Controller
{
    protected $tariffService;

    public function __construct(TariffService $tariffService)
    {
        $this->tariffService = $tariffService;
    }

    public function index()
    {
        $tariffs = Tariff::with(['route', 'vehicleType'])->latest()->paginate(10);
        return view('tariffs.index', compact('tariffs'));
    }

    public function create()
    {
        $routes = Route::all();
        $vehicleTypes = VehicleType::all();
        return view('tariffs.create', compact('routes', 'vehicleTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'nullable|exists:routes,id',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'price_per_km' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'price_per_cbm' => 'required|numeric|min:0',
            'fixed_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            $this->tariffService->createTariff($validated);
            return redirect()->route('tariffs.index')->with('success', 'Tariff created successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Failed to create tariff: ' . $e->getMessage());
        }
    }

    public function edit(Tariff $tariff)
    {
        $routes = Route::all();
        $vehicleTypes = VehicleType::all();
        return view('tariffs.edit', compact('tariff', 'routes', 'vehicleTypes'));
    }

    public function update(Request $request, Tariff $tariff)
    {
        $validated = $request->validate([
            'route_id' => 'nullable|exists:routes,id',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'price_per_km' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'price_per_cbm' => 'required|numeric|min:0',
            'fixed_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            $this->tariffService->updateTariff($tariff, $validated);
            return redirect()->route('tariffs.index')->with('success', 'Tariff updated successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Failed to update tariff: ' . $e->getMessage());
        }
    }

    public function destroy(Tariff $tariff)
    {
        try {
            $this->tariffService->deleteTariff($tariff);
            return redirect()->route('tariffs.index')->with('success', 'Tariff deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete tariff: ' . $e->getMessage());
        }
    }
}

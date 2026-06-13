<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ZoneController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Zone::with('warehouse');

        if ($user && !$user->hasRole('Super Admin')) {
            $query->whereHas('warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
            $warehouses = Warehouse::whereHas('users', fn($q) => $q->where('users.id', $user->id))->get();
        } else {
            $warehouses = Warehouse::all();
        }

        $zones = $query->latest()->paginate(10);
        return view('warehouse.zones.index', compact('zones', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('zones')->where(function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                })
            ],
            'description' => 'nullable|string',
        ]);

        Zone::create($validated);
        return redirect()->route('warehouse.zones.index')->with('success', 'Zone added successfully.');
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('zones')->where(function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                })->ignore($zone->id)
            ],
            'description' => 'nullable|string',
        ]);

        $zone->update($validated);
        return redirect()->route('warehouse.zones.index')->with('success', 'Zone updated successfully.');
    }

    public function destroy(Zone $zone)
    {
        if ($zone->racks()->count() > 0) {
            return redirect()->route('warehouse.zones.index')->with('error', 'Cannot delete zone with existing racks.');
        }
        $zone->delete();
        return redirect()->route('warehouse.zones.index')->with('success', 'Zone deleted successfully.');
    }
}

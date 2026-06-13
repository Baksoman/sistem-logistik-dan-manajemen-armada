<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Rack;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RackController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Rack::with(['zone', 'zone.warehouse']);

        if ($user && !$user->hasRole('Super Admin')) {
            $query->whereHas('zone.warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
            $zones = Zone::with('warehouse')->whereHas('warehouse.users', fn($q) => $q->where('users.id', $user->id))->get();
        } else {
            $zones = Zone::with('warehouse')->get();
        }

        $racks = $query->latest()->paginate(10);
        return view('warehouse.racks.index', compact('racks', 'zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('racks')->where(function ($query) use ($request) {
                    return $query->where('zone_id', $request->zone_id);
                })
            ],
            'description' => 'nullable|string',
        ]);

        Rack::create($validated);
        return redirect()->route('warehouse.racks.index')->with('success', 'Rack added successfully.');
    }

    public function update(Request $request, Rack $rack)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('racks')->where(function ($query) use ($request) {
                    return $query->where('zone_id', $request->zone_id);
                })->ignore($rack->id)
            ],
            'description' => 'nullable|string',
        ]);

        $rack->update($validated);
        return redirect()->route('warehouse.racks.index')->with('success', 'Rack updated successfully.');
    }

    public function destroy(Rack $rack)
    {
        // Check if there are any stock items using this rack
        $stockItemsCount = \App\Models\StockItem::where('rack_id', $rack->id)->count();
        if ($stockItemsCount > 0) {
            return redirect()->route('warehouse.racks.index')->with('error', 'Cannot delete rack that contains stock items.');
        }

        $rack->delete();
        return redirect()->route('warehouse.racks.index')->with('success', 'Rack deleted successfully.');
    }
}

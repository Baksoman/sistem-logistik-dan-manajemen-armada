<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\ShipmentSearchRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\QueryFilters\ShipmentFilter;

/**
 * API endpoint: GET /api/search/shipments
 *
 * Data scoping rule:
 * - Super Admin / Admin Logistik: see ALL shipments
 * - Driver: see ONLY shipments assigned to their driver profile
 *
 * The eager load chain here is:
 * driver.user → 2 hops (DriverProfile → User)
 * routeVersion.route → 2 hops (RouteVersion → Route)
 * vehicle.vehicleType → 2 hops (Vehicle → VehicleType)
 *
 * Laravel resolves nested eager loads efficiently using a single query per relation level.
 */
class ShipmentSearchController extends Controller
{
    public function __invoke(ShipmentSearchRequest $request, ShipmentFilter $filter)
    {
        $user = auth()->user();

        if ($user->hasRole('Driver')) {
            // Driver scope: only shipments assigned to their driver_profile id.
            $driverProfileId = $user->driverProfile?->id;
            $query = Shipment::where('driver_id', $driverProfileId);
        } else {
            $query = Shipment::query();
        }

        $shipments = $query
            ->with([
                'driver.user',
                'vehicle.vehicleType',
                'routeVersion.route',
            ])
            ->filter($filter)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ShipmentResource::collection($shipments);
    }
}

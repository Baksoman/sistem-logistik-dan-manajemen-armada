<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\StockMovementSearchRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use App\QueryFilters\StockMovementFilter;

class StockMovementSearchController extends Controller
{
    public function __invoke(StockMovementSearchRequest $request, StockMovementFilter $filter)
    {
        $user = auth()->user();
        $query = StockMovement::with(['stockItem.warehouse', 'creator'])
            ->filter($filter);

        if ($user && !$user->hasRole('Super Admin')) {
            $query->whereHas('stockItem.warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        // If the request specifically wants inbound or outbound, enforce it.
        // It's possible the route specifies type parameter programmatically before reaching here.
        if ($request->has('force_type')) {
            $query->where('type', $request->input('force_type'));
        }

        $movements = $query->latest()->paginate($request->integer('per_page', 15));

        return StockMovementResource::collection($movements);
    }
}

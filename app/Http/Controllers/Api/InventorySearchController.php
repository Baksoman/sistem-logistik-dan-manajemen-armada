<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\InventorySearchRequest;
use App\Http\Resources\InventoryResource;
use App\Models\StockItem;
use App\QueryFilters\InventoryFilter;

class InventorySearchController extends Controller
{
    public function __invoke(InventorySearchRequest $request, InventoryFilter $filter)
    {
        $user = auth()->user();
        $query = StockItem::with(['warehouse', 'category', 'unitType', 'zone', 'rack'])
            ->filter($filter);

        if ($user && !$user->hasRole('Super Admin')) {
            $query->whereHas('warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $items = $query->latest()->paginate($request->integer('per_page', 15));

        return InventoryResource::collection($items);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\ItemCategorySearchRequest;
use App\Http\Resources\ItemCategoryResource;
use App\Models\ItemCategory;
use App\QueryFilters\ItemCategoryFilter;

class ItemCategorySearchController extends Controller
{
    public function __invoke(ItemCategorySearchRequest $request, ItemCategoryFilter $filter)
    {
        $categories = ItemCategory::withCount('stockItems')
            ->filter($filter)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ItemCategoryResource::collection($categories);
    }
}

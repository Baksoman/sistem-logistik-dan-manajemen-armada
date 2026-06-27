<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemCategoryController extends Controller
{
    public function index(\App\Http\Requests\Search\ItemCategorySearchRequest $request, \App\QueryFilters\ItemCategoryFilter $filter)
    {
        $apiController = new \App\Http\Controllers\Api\ItemCategorySearchController();
        $initialData = $apiController($request, $filter)->response()->getData(true);
        return view('warehouse.categories.index', compact('initialData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:item_categories,name',
            'description' => 'nullable|string',
        ]);

        ItemCategory::create($validated);
        return redirect()->route('warehouse.categories.index')->with('success', 'Category added successfully.');
    }

    public function update(Request $request, ItemCategory $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('item_categories')->ignore($category->id)
            ],
            'description' => 'nullable|string',
        ]);

        $category->update($validated);
        return redirect()->route('warehouse.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ItemCategory $category)
    {
        // Check if items are attached
        if (\App\Models\StockItem::where('category_id', $category->id)->count() > 0) {
            return redirect()->route('warehouse.categories.index')->with('error', 'Cannot delete category because it has stock items.');
        }

        $category->delete();
        return redirect()->route('warehouse.categories.index')->with('success', 'Category deleted successfully.');
    }
}

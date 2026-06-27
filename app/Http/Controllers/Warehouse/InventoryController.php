<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Services\InventoryService;
use App\Http\Requests\Warehouse\StoreStockItemRequest;
use App\Http\Requests\Warehouse\UpdateStockItemRequest;
use App\Exports\Warehouse\InventoryExport;
use App\Http\Controllers\Api\InventorySearchController;
use App\Http\Requests\Search\InventorySearchRequest;
use App\QueryFilters\InventoryFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index(InventorySearchRequest $request, InventoryFilter $filter)
    {
        $apiController = new InventorySearchController();
        $initialData = $apiController($request, $filter)->response()->getData(true);
        $warehouses = $this->inventoryService->getWarehouses();
        $categories = $this->inventoryService->getItemCategories();
        $unitTypes = $this->inventoryService->getUnitTypes();
        $zones = $this->inventoryService->getZones();
        $racks = $this->inventoryService->getRacks();
        
        return view('warehouse.inventory.index', compact('initialData', 'warehouses', 'categories', 'unitTypes', 'zones', 'racks'));
    }

    public function store(StoreStockItemRequest $request)
    {
        $this->inventoryService->createStockItem($request->validated());
        return back()->with('success', 'Stock item added successfully.');
    }

    public function update(UpdateStockItemRequest $request, StockItem $inventory)
    {
        $this->inventoryService->updateStockItem($inventory, $request->validated());
        return back()->with('success', 'Stock item updated successfully.');
    }

    public function destroy(StockItem $inventory)
    {
        try {
            $this->inventoryService->deleteStockItem($inventory);
            return back()->with('success', 'Stock item deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportExcel()
    {
        return Excel::download(new InventoryExport, 'laporan-inventory-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $items = StockItem::with(['warehouse', 'category', 'unitType', 'zone', 'rack'])->get();
        $headings = ['ID', 'SKU', 'Name', 'Brand', 'Category', 'Warehouse', 'Zone', 'Rack', 'Qty Total', 'Qty Available', 'Unit', 'Weight (kg)', 'Volume (m³)'];
        $data = $items->map(fn($i) => [
            $i->id, $i->sku, $i->name, $i->brand ?? '-',
            $i->category->name ?? '-', $i->warehouse->name ?? '-',
            $i->zone->name ?? '-', $i->rack->name ?? '-',
            $i->quantity, $i->quantity - ($i->allocated_quantity ?? 0),
            $i->unitType->name ?? '-', $i->weight_kg, $i->volume_cbm,
        ]);

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => 'Laporan Data Inventory',
            'headings' => $headings,
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-inventory-' . now()->format('Ymd-His') . '.pdf');
    }
}

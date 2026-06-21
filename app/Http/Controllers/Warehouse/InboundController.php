<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Exports\Warehouse\InboundExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class InboundController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $movementsQuery = StockMovement::with(['stockItem.warehouse', 'stockItem.unitType', 'creator'])
            ->where('type', 'inbound')
            ->latest();
            
        $warehousesQuery = Warehouse::where('is_active', true);
        $stockItemsQuery = StockItem::with(['warehouse', 'unitType']);

        if ($user && !$user->hasRole('Super Admin')) {
            $movementsQuery->whereHas('stockItem.warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
            
            $warehousesQuery->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
            
            $stockItemsQuery->whereHas('warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $movements = $movementsQuery->paginate(15);
        $warehouses = $warehousesQuery->get();
        $stockItems = $stockItemsQuery->get();

        return view('warehouse.inbound.index', compact('movements', 'warehouses', 'stockItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|uuid|exists:stock_items,id',
            'quantity' => 'required|integer|min:1',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $stockItem = StockItem::findOrFail($validated['stock_item_id']);

        // Create movement record
        StockMovement::create([
            'stock_item_id' => $validated['stock_item_id'],
            'type' => 'inbound',
            'quantity' => $validated['quantity'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Update stock quantity
        $stockItem->increment('quantity', $validated['quantity']);

        return back()->with('success', "Inbound berhasil: +{$validated['quantity']} {$stockItem->name} telah ditambahkan ke stok.");
    }

    public function exportExcel()
    {
        return Excel::download(new InboundExport, 'laporan-inbound-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $movements = StockMovement::with(['stockItem.warehouse', 'stockItem.unitType', 'creator'])
            ->where('type', 'inbound')->latest()->get();
        $headings = ['ID', 'Reference No.', 'SKU', 'Item Name', 'Warehouse', 'Quantity', 'Unit', 'Received By', 'Notes', 'Date'];
        $data = $movements->map(fn($m) => [
            $m->id, $m->reference_number ?? '-',
            $m->stockItem->sku ?? '-', $m->stockItem->name ?? '-',
            $m->stockItem->warehouse->name ?? '-',
            $m->quantity, $m->stockItem->unitType->name ?? 'pcs',
            $m->creator->name ?? '-', $m->notes ?? '-',
            $m->created_at?->format('Y-m-d H:i') ?? '-',
        ]);

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => 'Laporan Inbound (Barang Masuk)',
            'headings' => $headings,
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-inbound-' . now()->format('Ymd-His') . '.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\StockItem;
use App\Models\Tariff;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = Order::with(['customer', 'originWarehouse', 'currentWarehouse'])
            ->latest()
            ->paginate(10);
            
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        $warehouses = Warehouse::all();
        $defaultTariff = Tariff::whereNull('route_id')->whereNull('vehicle_type_id')->first();
        
        return view('orders.create', compact('customers', 'warehouses', 'defaultTariff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'origin_warehouse_id' => 'required|exists:warehouses,id',
            'destination_address' => 'required|string',
            'destination_latitude' => 'nullable|numeric',
            'destination_longitude' => 'nullable|numeric',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'quoted_price' => 'nullable|numeric',
            'estimated_distance_km' => 'nullable|numeric',
        ]);

        try {
            $orderData = $request->only([
                'customer_id', 
                'origin_warehouse_id', 
                'destination_address',
                'destination_latitude',
                'destination_longitude',
                'quoted_price',
                'estimated_distance_km'
            ]);
            $orderData['created_by'] = auth()->id();

            $order = $this->orderService->createOrder($orderData, $request->items);

            return redirect()->route('orders.index')->with('success', 'Order created successfully. Order Number: ' . $order->order_number);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function getWarehouseItems(Warehouse $warehouse)
    {
        // Only return items that have available quantity > 0
        $items = StockItem::where('warehouse_id', $warehouse->id)
            ->whereRaw('(quantity - COALESCE(allocated_quantity, 0)) > 0')
            ->with('unitType')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'sku' => $item->sku,
                    'name' => $item->name,
                    'available_qty' => $item->quantity - ($item->allocated_quantity ?? 0),
                    'unit' => $item->unitType->name ?? 'pcs',
                    'weight_kg' => $item->weight_kg,
                    'volume_cbm' => $item->volume_cbm
                ];
            });

        return response()->json($items);
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'originWarehouse', 'currentWarehouse', 'items.stockItem', 'shipments.vehicle']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending Approval,Confirmed,Assigned,Arrived at Hub,Completed,Delivered,Cancelled'
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Order status updated successfully!');
    }
}

<?php

namespace App\Http\Controllers\Logistik;

use App\Http\Controllers\Controller;

use App\Models\Shipment;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Shipment Stats
        $shipmentStats = [
            'total' => Shipment::count(),
            'pending' => Shipment::where('status', 'Pending')->count(),
            'on_process' => Shipment::where('status', 'On Process')->count(),
            'delivered' => Shipment::where('status', 'Delivered')->count(),
        ];

        // 2. Order Stats
        $orderStats = [
            'total' => Order::count(),
            'draft' => Order::where('status', 'Draft')->count(),
            'confirmed' => Order::where('status', 'Confirmed')->count(),
            'completed' => Order::where('status', 'Completed')->count(),
        ];

        // 3. Chart Data: Shipments per day for the last 7 days
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $last7Days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $shipmentsPerDay = Shipment::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->pluck('count', 'date');

        $chartLabels = [];
        $chartData = [];

        foreach ($last7Days as $date) {
            $chartLabels[] = Carbon::parse($date)->format('D, M d');
            $chartData[] = $shipmentsPerDay[$date] ?? 0;
        }

        // 4. Recent Pending Shipments
        $recentShipments = Shipment::with(['driver.user', 'vehicle', 'routeVersion.route'])
            ->where('status', 'Pending')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.logistik.index', compact(
            'shipmentStats',
            'orderStats',
            'chartLabels',
            'chartData',
            'recentShipments'
        ));
    }
}

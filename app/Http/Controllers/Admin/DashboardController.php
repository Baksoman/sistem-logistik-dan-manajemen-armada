<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Financial Metrics
        $totalRevenue = \App\Models\Shipment::sum('total_cost') ?? 0;
        $totalExpense = \App\Models\OperationalCost::sum('amount') ?? 0;
        $netProfit = $totalRevenue - $totalExpense;

        // 2. Logistics Metrics
        $totalVehicles = \App\Models\Vehicle::count();
        $activeFleet = \App\Models\Vehicle::whereIn('status', ['Available', 'On Trip', 'available', 'on_trip'])->count();
        $fleetUtilization = $totalVehicles > 0 ? ($activeFleet / $totalVehicles) * 100 : 0;
        
        $totalWithSla = \App\Models\Shipment::whereNotNull('sla_target_at')->whereNotNull('completed_at')->count();
        $onTimeDeliveries = \App\Models\Shipment::whereNotNull('sla_target_at')
            ->whereNotNull('completed_at')
            ->whereColumn('completed_at', '<=', 'sla_target_at')
            ->count();
        $slaAchievement = $totalWithSla > 0 ? ($onTimeDeliveries / $totalWithSla) * 100 : 0;

        // 3. Warehouse Metrics
        $activeWarehouses = \App\Models\Warehouse::where('is_active', true)->count();
        $totalInventory = \App\Models\StockItem::sum('quantity') ?? 0;
        $pendingFulfillment = \App\Models\Order::whereIn('status', ['Draft', 'Confirmed', 'Pending'])->count();

        $stats = [
            'revenue' => $totalRevenue,
            'expense' => $totalExpense,
            'net_profit' => $netProfit,
            'fleet_utilization' => $fleetUtilization,
            'active_fleet' => $activeFleet,
            'total_vehicles' => $totalVehicles,
            'sla_achievement' => $slaAchievement,
            'active_warehouses' => $activeWarehouses,
            'total_inventory' => $totalInventory,
            'pending_fulfillment' => $pendingFulfillment,
        ];

        // 4. Chart Data: Revenue vs Expense for the last 7 days
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $last7Days->push(\Carbon\Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        // Daily Revenue
        $revenuePerDay = \App\Models\Shipment::select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('SUM(total_cost) as total'))
            ->where('created_at', '>=', \Carbon\Carbon::today()->subDays(6))
            ->groupBy('date')
            ->pluck('total', 'date');

        // Daily Expense
        $expensePerDay = \App\Models\OperationalCost::select(\Illuminate\Support\Facades\DB::raw('DATE(recorded_at) as date'), \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
            ->where('recorded_at', '>=', \Carbon\Carbon::today()->subDays(6))
            ->groupBy('date')
            ->pluck('total', 'date');

        $chartLabels = [];
        $revenueData = [];
        $expenseData = [];

        foreach ($last7Days as $date) {
            $chartLabels[] = \Carbon\Carbon::parse($date)->format('D, M d');
            $revenueData[] = $revenuePerDay[$date] ?? 0;
            $expenseData[] = $expensePerDay[$date] ?? 0;
        }

        // 5. Recent Activities
        $recentActivities = \App\Models\ShipmentCheckpoint::with('shipment')
            ->latest('recorded_at')
            ->take(5)
            ->get();

        return view('dashboard.admin.index', compact('stats', 'chartLabels', 'revenueData', 'expenseData', 'recentActivities'));
    }
}

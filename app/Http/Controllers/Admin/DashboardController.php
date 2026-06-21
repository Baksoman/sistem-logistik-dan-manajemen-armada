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

        // 4. Chart Data: Revenue vs Expense
        $trend = request('trend', 'last_7_days');
        $chartLabels = [];
        $revenueData = [];
        $expenseData = [];

        if ($trend === 'last_6_months' || $trend === 'last_3_months') {
            $months = $trend === 'last_6_months' ? 6 : 3;
            $periods = collect();
            for ($i = $months - 1; $i >= 0; $i--) {
                $periods->push(\Carbon\Carbon::today()->startOfMonth()->subMonths($i)->format('Y-m'));
            }
            
            $revenuePerPeriod = \App\Models\Shipment::select(\Illuminate\Support\Facades\DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'), \Illuminate\Support\Facades\DB::raw('SUM(total_cost) as total'))
                ->where('created_at', '>=', \Carbon\Carbon::today()->startOfMonth()->subMonths($months - 1))
                ->groupBy('period')
                ->pluck('total', 'period');

            $expensePerPeriod = \App\Models\OperationalCost::select(\Illuminate\Support\Facades\DB::raw('DATE_FORMAT(recorded_at, "%Y-%m") as period'), \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
                ->where('recorded_at', '>=', \Carbon\Carbon::today()->startOfMonth()->subMonths($months - 1))
                ->groupBy('period')
                ->pluck('total', 'period');

            foreach ($periods as $period) {
                $chartLabels[] = \Carbon\Carbon::createFromFormat('Y-m', $period)->format('M Y');
                $revenueData[] = $revenuePerPeriod[$period] ?? 0;
                $expenseData[] = $expensePerPeriod[$period] ?? 0;
            }
            $trendTitle = 'Financial Trends (Last ' . $months . ' Months)';
        } elseif ($trend === 'last_month') {
            // Last 4 weeks
            $periods = collect();
            for ($i = 3; $i >= 0; $i--) {
                $startOfWeek = \Carbon\Carbon::today()->startOfWeek()->subWeeks($i);
                $periods->push($startOfWeek->format('Y-m-d'));
            }

            $revenueRecords = \App\Models\Shipment::where('created_at', '>=', \Carbon\Carbon::today()->startOfWeek()->subWeeks(3))->get();
            $expenseRecords = \App\Models\OperationalCost::where('recorded_at', '>=', \Carbon\Carbon::today()->startOfWeek()->subWeeks(3))->get();

            $revenuePerPeriod = [];
            foreach ($revenueRecords as $rec) {
                $start = \Carbon\Carbon::parse($rec->created_at)->startOfWeek()->format('Y-m-d');
                $revenuePerPeriod[$start] = ($revenuePerPeriod[$start] ?? 0) + $rec->total_cost;
            }

            $expensePerPeriod = [];
            foreach ($expenseRecords as $rec) {
                $start = \Carbon\Carbon::parse($rec->recorded_at)->startOfWeek()->format('Y-m-d');
                $expensePerPeriod[$start] = ($expensePerPeriod[$start] ?? 0) + $rec->amount;
            }

            foreach ($periods as $period) {
                $endOfWeek = \Carbon\Carbon::parse($period)->endOfWeek()->format('M d');
                $chartLabels[] = \Carbon\Carbon::parse($period)->format('M d') . ' - ' . $endOfWeek;
                $revenueData[] = $revenuePerPeriod[$period] ?? 0;
                $expenseData[] = $expensePerPeriod[$period] ?? 0;
            }
            $trendTitle = 'Financial Trends (Last Month)';
        } else {
            // last_7_days
            $periods = collect();
            for ($i = 6; $i >= 0; $i--) {
                $periods->push(\Carbon\Carbon::today()->subDays($i)->format('Y-m-d'));
            }

            $revenuePerPeriod = \App\Models\Shipment::select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as period'), \Illuminate\Support\Facades\DB::raw('SUM(total_cost) as total'))
                ->where('created_at', '>=', \Carbon\Carbon::today()->subDays(6))
                ->groupBy('period')
                ->pluck('total', 'period');

            $expensePerPeriod = \App\Models\OperationalCost::select(\Illuminate\Support\Facades\DB::raw('DATE(recorded_at) as period'), \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
                ->where('recorded_at', '>=', \Carbon\Carbon::today()->subDays(6))
                ->groupBy('period')
                ->pluck('total', 'period');

            foreach ($periods as $period) {
                $chartLabels[] = \Carbon\Carbon::parse($period)->format('D, M d');
                $revenueData[] = $revenuePerPeriod[$period] ?? 0;
                $expenseData[] = $expensePerPeriod[$period] ?? 0;
            }
            $trendTitle = 'Financial Trends (Last 7 Days)';
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'chartLabels' => $chartLabels,
                'revenueData' => $revenueData,
                'expenseData' => $expenseData,
                'trendTitle' => $trendTitle
            ]);
        }

        // 5. Recent Activities
        $recentActivities = \App\Models\ShipmentCheckpoint::with('shipment')
            ->latest('recorded_at')
            ->take(5)
            ->get();

        return view('dashboard.admin.index', compact('stats', 'chartLabels', 'revenueData', 'expenseData', 'recentActivities', 'trend', 'trendTitle'));
    }
}

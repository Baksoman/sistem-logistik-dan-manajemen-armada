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

        // 5. Dashboard Analytics (Cost, SLA, Profitabilitas)
        $totalDistance = Shipment::sum('total_distance_km') ?? 0;
        $totalRevenue = Shipment::sum('total_cost') ?? 0;
        $totalExpense = \App\Models\OperationalCost::sum('amount') ?? 0;
        
        $netProfit = $totalRevenue - $totalExpense;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        
        $costPerKm = $totalDistance > 0 ? $totalExpense / $totalDistance : 0;

        // SLA Performance
        $totalWithSla = Shipment::whereNotNull('sla_target_at')->whereNotNull('completed_at')->count();
        $onTimeDeliveries = Shipment::whereNotNull('sla_target_at')
            ->whereNotNull('completed_at')
            ->whereColumn('completed_at', '<=', 'sla_target_at')
            ->count();
            
        $slaAchievement = $totalWithSla > 0 ? ($onTimeDeliveries / $totalWithSla) * 100 : 0;

        $analytics = [
            'total_distance' => $totalDistance,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'cost_per_km' => $costPerKm,
            'sla_achievement' => $slaAchievement,
            'total_with_sla' => $totalWithSla,
            'on_time_deliveries' => $onTimeDeliveries,
        ];

        // Advanced Analytics
        // 1. OTIF (On-Time In-Full)
        // For simplicity, we calculate OTIF % based on delivered shipments that met SLA.
        $totalDeliveredShipments = Shipment::where('status', 'Delivered')->count();
        $otifDeliveries = Shipment::where('status', 'Delivered')
            ->whereNotNull('sla_target_at')
            ->whereNotNull('completed_at')
            ->whereColumn('completed_at', '<=', 'sla_target_at')
            ->count();
        $otifAchievement = $totalDeliveredShipments > 0 ? ($otifDeliveries / $totalDeliveredShipments) * 100 : 0;

        // 2. Avg Transit Time
        $completedShipmentsQuery = Shipment::whereNotNull('started_at')->whereNotNull('completed_at');
        $totalTransitHours = 0;
        $transitCount = 0;
        $completedShipmentsList = $completedShipmentsQuery->take(100)->get();
        foreach ($completedShipmentsList as $ship) {
            $start = Carbon::parse($ship->started_at);
            $end = Carbon::parse($ship->completed_at);
            if ($end->greaterThan($start)) {
                $totalTransitHours += $start->diffInHours($end);
                $transitCount++;
            }
        }
        $avgTransitTime = $transitCount > 0 ? ($totalTransitHours / $transitCount) : 0;

        // 3. Freight Cost per Shipment
        $freightCostPerShipment = $totalDeliveredShipments > 0 ? ($totalExpense / $totalDeliveredShipments) : 0;

        // 4. Fleet Utilization
        $totalVehicles = \App\Models\Vehicle::count();
        $activeFleet = \App\Models\Vehicle::whereIn('status', ['Available', 'On Trip', 'available', 'on_trip'])->count();
        $fleetUtilization = $totalVehicles > 0 ? ($activeFleet / $totalVehicles) * 100 : 0;

        // 5. Fuel Efficiency (Cost per KM for Fuel)
        $fuelCost = \App\Models\OperationalCost::whereHas('category', function($q) {
            $q->where('name', 'Fuel');
        })->sum('amount') ?? 0;
        $fuelEfficiency = $totalDistance > 0 ? ($fuelCost / $totalDistance) : 0;

        // 6. Delivery Status
        $deliveryStatusData = [
            'pending' => $shipmentStats['pending'],
            'on_process' => $shipmentStats['on_process'],
            'delivered' => $shipmentStats['delivered'],
        ];

        $advancedAnalytics = [
            'otif_achievement' => $otifAchievement,
            'avg_transit_time' => $avgTransitTime,
            'freight_cost_per_shipment' => $freightCostPerShipment,
            'fleet_utilization' => $fleetUtilization,
            'fuel_efficiency' => $fuelEfficiency,
            'delivery_status' => $deliveryStatusData,
        ];

        return view('dashboard.logistik.index', compact(
            'shipmentStats',
            'orderStats',
            'chartLabels',
            'chartData',
            'recentShipments',
            'analytics',
            'advancedAnalytics'
        ));
    }
}

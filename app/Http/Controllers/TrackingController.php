<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipment;

class TrackingController extends Controller
{
    public function search(Request $request)
    {
        $trackingId = trim($request->input('tracking_id'));

        if (!$trackingId) {
            return view('welcome', [
                'error' => 'Silakan masukkan nomor resi (Order ID / Shipment ID).',
                'trackingId' => null
            ]);
        }

        // Try searching Order first
        $order = Order::with(['originWarehouse', 'currentWarehouse', 'histories' => function($q) {
            $q->orderBy('created_at', 'desc');
        }, 'shipments' => function($q) {
            $q->whereIn('shipments.status', ['On Transit', 'Out for Delivery', 'At Hub'])->with(['gpsHistory' => function($q) {
                $q->orderBy('recorded_at', 'desc')->take(1);
            }]);
        }])->where('order_number', $trackingId)->first();

        if ($order) {
            return view('welcome', [
                'type' => 'order',
                'data' => $order,
                'trackingId' => $trackingId
            ]);
        }

        // If not Order, try Shipment
        $shipment = Shipment::with(['vehicle', 'driver', 'checkpoints' => function($q) {
            $q->orderBy('recorded_at', 'desc');
        }, 'gpsHistory' => function($q) {
            $q->orderBy('recorded_at', 'desc')->take(1);
        }])->where('shipment_number', $trackingId)->first();

        if ($shipment) {
            return view('welcome', [
                'type' => 'shipment',
                'data' => $shipment,
                'trackingId' => $trackingId
            ]);
        }

        // Not found
        return view('welcome', [
            'error' => 'Resi tidak ditemukan. Pastikan nomor yang dimasukkan benar.',
            'trackingId' => $trackingId
        ]);
    }
}

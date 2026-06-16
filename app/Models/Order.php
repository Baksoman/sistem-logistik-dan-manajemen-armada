<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_number',
        'customer_id',
        'created_by',
        'origin_warehouse_id',
        'destination_address',
        'destination_latitude',
        'destination_longitude',
        'total_weight',
        'total_volume',
        'quoted_price',
        'estimated_distance_km',
        'status',
        'current_warehouse_id',
        'tracking_status'
    ];

    protected $casts = [
        'tracking_status' => \App\Enums\OrderTrackingStatus::class,
    ];

    public function customer() { return $this->belongsTo(Customer::class); }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function originWarehouse() { return $this->belongsTo(Warehouse::class, 'origin_warehouse_id'); }

    public function currentWarehouse() { return $this->belongsTo(Warehouse::class, 'current_warehouse_id'); }

    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function items() { return $this->hasMany(OrderItem::class); }

    public function shipments() { return $this->belongsToMany(Shipment::class, 'shipment_orders')
                                        ->withPivot('status', 'dropoff_warehouse_id'); }

    public function histories() { return $this->hasMany(OrderHistory::class)->orderBy('created_at', 'desc'); }
    
    public function proofOfDeliveries() { return $this->hasMany(ProofOfDelivery::class); }

    protected static function booted()
    {
        static::updated(function ($order) {
            if ($order->isDirty('status')) {
                \Illuminate\Support\Facades\DB::table('order_histories')->insert([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'description' => "Status changed to " . $order->status,
                    'location' => $order->currentWarehouse->name ?? null,
                    'user_id' => auth()->id() ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}

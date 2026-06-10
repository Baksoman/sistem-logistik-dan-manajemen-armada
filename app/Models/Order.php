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
        'status'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function originWarehouse() { return $this->belongsTo(Warehouse::class, 'origin_warehouse_id'); }

    public function orderItems() { return $this->hasMany(OrderItem::class); }

    public function shipments() { return $this->belongsToMany(Shipment::class, 'shipment_orders'); }

}

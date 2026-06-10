<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'stock_item_id',
        'quantity',
        'weight_kg',
        'volume_cbm'
    ];

    public function order() { return $this->belongsTo(Order::class); }

    public function stockItem() { return $this->belongsTo(StockItem::class); }

}

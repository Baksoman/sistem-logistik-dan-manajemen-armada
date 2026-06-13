<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'warehouse_id',
        'category_id',
        'unit_type_id',
        'sku',
        'gtin',
        'name',
        'quantity',
        'min_quantity',
        'weight_kg',
        'volume_cbm',
        'zone',
        'bin_location'
    ];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }

    public function category() { return $this->belongsTo(ItemCategory::class); }

    public function unitType() { return $this->belongsTo(UnitType::class); }

}

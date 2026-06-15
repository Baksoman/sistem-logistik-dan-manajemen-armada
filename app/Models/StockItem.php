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
        'upc',
        'brand',
        'name',
        'quantity',
        'allocated_quantity',
        'min_quantity',
        'weight_kg',
        'volume_cbm',
        'zone_id',
        'rack_id'
    ];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }

    public function category() { return $this->belongsTo(ItemCategory::class); }

    public function unitType() { return $this->belongsTo(UnitType::class); }

    public function zone() { return $this->belongsTo(Zone::class); }

    public function rack() { return $this->belongsTo(Rack::class); }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->sku)) {
                $category = ItemCategory::find($item->category_id);
                
                $getPrefix = function($str) {
                    $cleaned = preg_replace('/[^A-Za-z]/', '', $str);
                    return strtoupper(substr($cleaned, 0, 3));
                };

                $catPrefix = $category ? $getPrefix($category->name) : 'XXX';
                $brandPrefix = $item->brand ? $getPrefix($item->brand) : 'XXX';
                $namePrefix = $item->name ? $getPrefix($item->name) : 'XXX';

                // Base SKU without the numeric ID
                $baseSku = "{$catPrefix}-{$brandPrefix}-{$namePrefix}";

                // Find all existing SKUs starting with this base pattern
                $existingSkus = static::where('sku', 'LIKE', "{$baseSku}-%")->pluck('sku')->toArray();
                
                $usedIds = [];
                foreach ($existingSkus as $existingSku) {
                    $parts = explode('-', $existingSku);
                    $idPart = end($parts);
                    if (is_numeric($idPart)) {
                        $usedIds[] = (int)$idPart;
                    }
                }

                $nextId = 1;
                while (in_array($nextId, $usedIds)) {
                    $nextId++;
                }

                $item->sku = $baseSku . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
        });
    }

}

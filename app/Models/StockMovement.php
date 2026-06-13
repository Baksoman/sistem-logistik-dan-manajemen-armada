<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $stock_item_id
 * @property string $type
 * @property int $quantity
 * @property string|null $reference_number
 * @property string|null $notes
 * @property string|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read StockItem $stockItem
 * @property-read User|null $creator
 */
class StockMovement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'stock_item_id',
        'type',
        'quantity',
        'reference_number',
        'notes',
        'created_by',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

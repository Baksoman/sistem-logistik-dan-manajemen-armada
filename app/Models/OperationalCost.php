<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalCost extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'shipment_id',
        'category_id',
        'amount',
        'description',
        'recorded_at'
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime'
        ];
    }

    public function shipment() { return $this->belongsTo(Shipment::class); }

    public function category() { return $this->belongsTo(CostCategory::class); }

}

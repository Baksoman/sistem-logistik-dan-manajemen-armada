<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'price_per_km' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'price_per_cbm' => 'decimal:2',
        'fixed_price' => 'decimal:2',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }
}

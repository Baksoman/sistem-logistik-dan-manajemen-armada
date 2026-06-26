<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vehicle_type_id',
        'plate_number',
        'brand',
        'model',
        'year',
        'capacity_kg',
        'capacity_volume_cbm',
        'fuel_cost_per_km',
        'fuel_type',
        'status',
        'kir_expired_at',
        'stnk_expired_at'
    ];

    protected $casts = [
        'kir_expired_at' => 'date',
        'stnk_expired_at' => 'date',
    ];

    public function vehicleType() { return $this->belongsTo(VehicleType::class); }

    public function shipments() { return $this->hasMany(Shipment::class); }

}

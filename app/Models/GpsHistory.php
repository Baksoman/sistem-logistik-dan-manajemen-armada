<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpsHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'shipment_id',
        'driver_id',
        'latitude',
        'longitude',
        'speed',
        'heading',
        'recorded_at'
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime'
        ];
    }

    public function shipment() { return $this->belongsTo(Shipment::class); }

    public function driver() { return $this->belongsTo(DriverProfile::class, 'driver_id'); }

}

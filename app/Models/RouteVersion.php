<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteVersion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'route_id',
        'source_api',
        'distance_km',
        'duration_min',
        'polyline_geojson',
        'waypoints',
        'calculated_at'
    ];

    protected function casts(): array
    {
        return [
            'polyline_geojson' => 'array',
            'waypoints' => 'array',
            'calculated_at' => 'datetime'
        ];
    }

    public function route() { return $this->belongsTo(Route::class); }

    public function shipments() { return $this->hasMany(Shipment::class); }

}

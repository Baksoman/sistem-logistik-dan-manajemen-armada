<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'shipment_number',
        'driver_id',
        'vehicle_id',
        'route_version_id',
        'status',
        'total_distance_km',
        'total_cost',
        'cost_per_km',
        'sla_target_at',
        'started_at',
        'completed_at'
    ];

    protected function casts(): array
    {
        return [
            'sla_target_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime'
        ];
    }

    protected $appends = ['sla_status'];

    public function getSlaStatusAttribute()
    {
        if (!$this->sla_target_at) {
            return 'No Target';
        }

        if (!$this->completed_at) {
            if (now() > $this->sla_target_at) {
                return 'Late (Ongoing)';
            }
            if (now()->addHours(2) >= $this->sla_target_at) {
                return 'At Risk';
            }
            return 'On Track';
        }

        return $this->completed_at <= $this->sla_target_at ? 'On Time' : 'Late';
    }

    public function driver() { return $this->belongsTo(DriverProfile::class, 'driver_id'); }

    public function vehicle() { return $this->belongsTo(Vehicle::class); }

    public function routeVersion() { return $this->belongsTo(RouteVersion::class); }

    public function operationalCosts() { return $this->hasMany(OperationalCost::class); }
    
    public function checkpoints() { return $this->hasMany(ShipmentCheckpoint::class)->orderBy('recorded_at', 'desc'); }

    public function gpsHistory() { return $this->hasMany(GpsHistory::class); }

    public function shipmentCheckpoints() { return $this->hasMany(ShipmentCheckpoint::class); }

    public function proofOfDelivery() { return $this->hasOne(ProofOfDelivery::class); }

    public function orders() { return $this->belongsToMany(Order::class, 'shipment_orders'); }

}

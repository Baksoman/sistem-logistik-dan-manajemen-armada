<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'nik',
        'phone',
        'address',
        'license_number',
        'license_type',
        'license_expired_at',
        'rating',
        'status',
        'joined_at'
    ];

    protected function casts(): array
    {
        return [
            'license_expired_at' => 'date',
            'joined_at' => 'date'
        ];
    }

    public function user() { return $this->belongsTo(User::class); }

    public function shipments() { return $this->hasMany(Shipment::class, 'driver_id'); }

}

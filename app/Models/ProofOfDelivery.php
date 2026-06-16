<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProofOfDelivery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'shipment_id',
        'order_id',
        'receiver_name',
        'receiver_phone',
        'notes',
        'latitude',
        'longitude',
        'delivered_at'
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime'
        ];
    }

    public function shipment() { return $this->belongsTo(Shipment::class); }
    
    public function order() { return $this->belongsTo(Order::class); }

    public function podPhotos() { return $this->hasMany(PodPhoto::class); }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'proof_of_delivery_id',
        'photo_path',
        'uploaded_at'
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime'
        ];
    }

    public function proofOfDelivery() { return $this->belongsTo(ProofOfDelivery::class); }

}

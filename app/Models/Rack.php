<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'zone_id',
        'name',
        'description',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}

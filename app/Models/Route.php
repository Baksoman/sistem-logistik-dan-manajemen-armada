<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'route_code',
        'route_type',
        'origin_name',
        'destination_name'
    ];

    public function routeVersions() { return $this->hasMany(RouteVersion::class); }

}

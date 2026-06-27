<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $route_code
 * @property string $route_type
 * @property string $origin_name
 * @property string $destination_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RouteVersion[] $routeVersions
 */
class Route extends Model
{
    use HasFactory, HasUuids, Filterable;

    protected $fillable = [
        'route_code',
        'route_type',
        'origin_name',
        'destination_name',
        'toll_cost',
        'ferry_cost',
        'is_master'
    ];

    public function routeVersions() { return $this->hasMany(RouteVersion::class); }

}

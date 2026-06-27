<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory, HasUuids, Filterable;

    protected $fillable = [
        'code',
        'name',
        'address',
        'latitude',
        'longitude',
        'is_active'
    ];

    public function users() { return $this->belongsToMany(User::class, 'warehouse_users'); }

    public function stockItems() { return $this->hasMany(StockItem::class); }

    public function zones() { return $this->hasMany(Zone::class); }

    public function racks() { return $this->hasManyThrough(Rack::class, Zone::class); }

}

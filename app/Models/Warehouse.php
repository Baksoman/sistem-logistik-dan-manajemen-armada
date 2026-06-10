<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory, HasUuids;

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

}

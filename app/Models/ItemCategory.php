<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory, HasUuids, Filterable;

    protected $fillable = [
        'name',
        'description'
    ];

    public function stockItems() { return $this->hasMany(StockItem::class, 'category_id'); }

}

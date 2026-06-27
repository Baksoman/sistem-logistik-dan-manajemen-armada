<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class InventoryFilter extends QueryFilter
{
    public function search(string $value): void
    {
        $this->builder->where(function (Builder $query) use ($value) {
            $query->where('sku', 'like', "%{$value}%")
                  ->orWhere('name', 'like', "%{$value}%")
                  ->orWhereHas('warehouse', function ($q) use ($value) {
                      $q->where('name', 'like', "%{$value}%");
                  })
                  ->orWhereHas('category', function ($q) use ($value) {
                      $q->where('name', 'like', "%{$value}%");
                  });
        });
    }

    public function warehouse_id(string $value): void
    {
        $this->builder->where('warehouse_id', $value);
    }

    public function category_id(string $value): void
    {
        $this->builder->where('category_id', $value);
    }

    public function is_low_stock(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->builder->whereColumn('quantity', '<=', 'min_quantity');
        }
    }

    public function date_from(string $value): void
    {
        $this->builder->whereDate('created_at', '>=', $value);
    }

    public function date_to(string $value): void
    {
        $this->builder->whereDate('created_at', '<=', $value);
    }
}

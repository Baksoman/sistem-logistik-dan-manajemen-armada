<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class StockMovementFilter extends QueryFilter
{
    public function search(string $value): void
    {
        $this->builder->where(function (Builder $query) use ($value) {
            $query->where('reference_number', 'like', "%{$value}%")
                  ->orWhereHas('stockItem', function ($q) use ($value) {
                      $q->where('name', 'like', "%{$value}%")
                        ->orWhere('sku', 'like', "%{$value}%");
                  });
        });
    }

    public function type(string $value): void
    {
        $this->builder->where('type', $value);
    }

    public function warehouse_id(string $value): void
    {
        $this->builder->whereHas('stockItem', function ($q) use ($value) {
            $q->where('warehouse_id', $value);
        });
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

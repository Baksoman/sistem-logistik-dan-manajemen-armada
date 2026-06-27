<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class WarehouseFilter extends QueryFilter
{
    public function search(string $value): void
    {
        $this->builder->where(function (Builder $query) use ($value) {
            $query->where('code', 'like', "%{$value}%")
                  ->orWhere('name', 'like', "%{$value}%")
                  ->orWhere('address', 'like', "%{$value}%");
        });
    }

    public function is_active(string $value): void
    {
        $this->builder->where('is_active', filter_var($value, FILTER_VALIDATE_BOOLEAN));
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

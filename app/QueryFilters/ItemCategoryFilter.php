<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class ItemCategoryFilter extends QueryFilter
{
    public function search(string $value): void
    {
        $this->builder->where(function (Builder $query) use ($value) {
            $query->where('name', 'like', "%{$value}%")
                  ->orWhere('description', 'like', "%{$value}%");
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

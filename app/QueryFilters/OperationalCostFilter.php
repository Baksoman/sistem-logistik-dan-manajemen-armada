<?php

namespace App\QueryFilters;

/**
 * Filter class for Operational Costs.
 *
 * Searchable fields: shipment_number, driver name, category name
 * Filterable fields: category_id, driver_id, status (shipment), date_from, date_to
 */
class OperationalCostFilter extends QueryFilter
{
    /**
     * Keyword search across shipment number, driver name, and category name.
     */
    protected function search(string $value): void
    {
        $this->builder->where(function ($q) use ($value) {
            $q->whereHas('shipment', function ($sq) use ($value) {
                $sq->where('shipment_number', 'LIKE', "%{$value}%")
                   ->orWhereHas('driver.user', function ($dq) use ($value) {
                       $dq->where('name', 'LIKE', "%{$value}%");
                   });
            })->orWhereHas('category', function ($cq) use ($value) {
                $cq->where('name', 'LIKE', "%{$value}%");
            });
        });
    }

    /**
     * Filter by cost category.
     */
    protected function category_id(string $value): void
    {
        $this->builder->where('category_id', $value);
    }

    /**
     * Filter by driver ID.
     */
    protected function driver_id(string $value): void
    {
        $this->builder->whereHas('shipment', function ($q) use ($value) {
            $q->where('driver_id', $value);
        });
    }

    /**
     * Filter by shipment status.
     */
    protected function status(string $value): void
    {
        $this->builder->whereHas('shipment', function ($q) use ($value) {
            $q->where('status', $value);
        });
    }

    /**
     * Filter by recorded date (start).
     */
    protected function date_from(string $value): void
    {
        $this->builder->whereDate('recorded_at', '>=', $value);
    }

    /**
     * Filter by recorded date (end).
     */
    protected function date_to(string $value): void
    {
        $this->builder->whereDate('recorded_at', '<=', $value);
    }
}

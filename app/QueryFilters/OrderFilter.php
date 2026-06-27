<?php

namespace App\QueryFilters;

/**
 * Filter class for the Order module.
 *
 * Searchable fields: order_number, customer.company_name, destination_address
 * Filterable fields: status, origin_warehouse_id, customer_id, created_from, created_to
 */
class OrderFilter extends QueryFilter
{
    /**
     * Keyword search across the most meaningful order identification fields.
     *
     * - order_number: direct identifier for logistics operators
     * - customer.company_name: operators often search by client
     * - destination_address: operators may search by delivery location keyword
     *
     * Wrapped in a closure to isolate OR conditions from other WHERE clauses,
     * preventing incorrect query results when other filters are chained.
     */
    protected function search(string $value): void
    {
        $this->builder->where(function ($q) use ($value) {
            $q->where('order_number', 'LIKE', "%{$value}%")
              ->orWhere('destination_address', 'LIKE', "%{$value}%")
              ->orWhereHas('customer', function ($cq) use ($value) {
                  $cq->where('company_name', 'LIKE', "%{$value}%");
              });
        });
    }

    /**
     * Filter by order status.
     * Valid values: Draft, Pending Approval, Confirmed, Assigned, Arrived at Hub, Completed, Cancelled.
     */
    protected function status(string $value): void
    {
        $this->builder->where('status', $value);
    }

    /**
     * Filter by origin warehouse.
     */
    protected function warehouse_id(string $value): void
    {
        $this->builder->where('origin_warehouse_id', $value);
    }

    /**
     * Filter by customer.
     */
    protected function customer_id(string $value): void
    {
        $this->builder->where('customer_id', $value);
    }

    /**
     * Start of creation date range (inclusive).
     * Uses whereDate to ignore time component.
     */
    protected function created_from(string $value): void
    {
        $this->builder->whereDate('created_at', '>=', $value);
    }

    /**
     * End of creation date range (inclusive).
     */
    protected function created_to(string $value): void
    {
        $this->builder->whereDate('created_at', '<=', $value);
    }
}

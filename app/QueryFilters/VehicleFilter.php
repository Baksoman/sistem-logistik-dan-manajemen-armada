<?php

namespace App\QueryFilters;

/**
 * Filter class for the Vehicle module.
 *
 * Searchable fields: plate_number, brand, model
 * Filterable fields: vehicle_type_id, status, fuel_type, capacity_min, capacity_max
 */
class VehicleFilter extends QueryFilter
{
    /**
     * Keyword search across the primary vehicle identification fields.
     *
     * - plate_number: the most common way to identify a specific truck/van
     * - brand + model: operators often search by vehicle make
     */
    protected function search(string $value): void
    {
        $this->builder->where(function ($q) use ($value) {
            $q->where('plate_number', 'LIKE', "%{$value}%")
              ->orWhere('brand', 'LIKE', "%{$value}%")
              ->orWhere('model', 'LIKE', "%{$value}%");
        });
    }

    /**
     * Filter by vehicle type (e.g., Truck, Pickup, Van, Kapal).
     * References vehicle_types.id.
     */
    protected function vehicle_type_id(string $value): void
    {
        $this->builder->where('vehicle_type_id', $value);
    }

    /**
     * Filter by operational status.
     * Valid values: available, on_trip, maintenance, inactive.
     */
    protected function status(string $value): void
    {
        $this->builder->where('status', $value);
    }

    /**
     * Filter by fuel type (e.g., Solar, Pertamax, Bensin, Listrik).
     */
    protected function fuel_type(string $value): void
    {
        $this->builder->where('fuel_type', $value);
    }

    /**
     * Filter vehicles with a minimum carrying capacity (kg).
     * Useful when assigning vehicles for heavy loads.
     */
    protected function capacity_min(string $value): void
    {
        $this->builder->where('capacity_kg', '>=', (float) $value);
    }

    /**
     * Filter vehicles with a maximum carrying capacity (kg).
     * Useful when looking for lighter, more cost-effective options.
     */
    protected function capacity_max(string $value): void
    {
        $this->builder->where('capacity_kg', '<=', (float) $value);
    }
}

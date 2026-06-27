<?php

namespace App\QueryFilters;

/**
 * Filter class for the Shipment module.
 *
 * Searchable fields: shipment_number, driver.user.name, vehicle.plate_number
 * Filterable fields: status, driver_id, vehicle_id, route_version_id, started_from, started_to
 *
 * Note: Shipment.driver_id references driver_profiles.id (not users.id directly).
 * The 2-hop relation is: Shipment → DriverProfile → User.
 */
class ShipmentFilter extends QueryFilter
{
    /**
     * Keyword search across the most operationally relevant shipment fields.
     *
     * - shipment_number: primary identifier
     * - vehicle.plate_number: operators track by truck/van
     * - driver.user.name: operators track by driver name (2-hop relation)
     */
    protected function search(string $value): void
    {
        $this->builder->where(function ($q) use ($value) {
            $q->where('shipment_number', 'LIKE', "%{$value}%")
              ->orWhereHas('vehicle', function ($vq) use ($value) {
                  $vq->where('plate_number', 'LIKE', "%{$value}%");
              })
              ->orWhereHas('driver.user', function ($dq) use ($value) {
                  $dq->where('name', 'LIKE', "%{$value}%");
              });
        });
    }

    /**
     * Filter by shipment status.
     * Valid values: Pending, On Process, Delivered, Failed.
     */
    protected function status(string $value): void
    {
        $this->builder->where('status', $value);
    }

    /**
     * Filter by driver (driver_profiles.id).
     */
    protected function driver_id(string $value): void
    {
        $this->builder->where('driver_id', $value);
    }

    /**
     * Filter by vehicle.
     */
    protected function vehicle_id(string $value): void
    {
        $this->builder->where('vehicle_id', $value);
    }

    /**
     * Filter by route version (specific calculated route).
     */
    protected function route_version_id(string $value): void
    {
        $this->builder->where('route_version_id', $value);
    }

    /**
     * Filter shipments that started on or after this date.
     */
    protected function started_from(string $value): void
    {
        $this->builder->whereDate('started_at', '>=', $value);
    }

    /**
     * Filter shipments that started on or before this date.
     */
    protected function started_to(string $value): void
    {
        $this->builder->whereDate('started_at', '<=', $value);
    }

    /**
     * Filter by SLA status. Useful for monitoring late or at-risk shipments.
     * Since sla_status is a computed attribute (not a DB column), we
     * translate it into equivalent database conditions.
     *
     * "late"     → completed_at > sla_target_at OR (completed_at IS NULL AND NOW() > sla_target_at)
     * "on_time"  → completed_at <= sla_target_at
     * "at_risk"  → completed_at IS NULL AND sla_target_at BETWEEN NOW() AND NOW() + 2 hours
     */
    protected function sla_status(string $value): void
    {
        match ($value) {
            'late' => $this->builder->where(function ($q) {
                $q->where(function ($inner) {
                    // Already completed but late
                    $inner->whereNotNull('completed_at')
                          ->whereColumn('completed_at', '>', 'sla_target_at');
                })->orWhere(function ($inner) {
                    // Still running but already past target
                    $inner->whereNull('completed_at')
                          ->whereNotNull('sla_target_at')
                          ->whereRaw('NOW() > sla_target_at');
                });
            }),
            'on_time' => $this->builder->whereNotNull('completed_at')
                                       ->whereColumn('completed_at', '<=', 'sla_target_at'),
            'at_risk'  => $this->builder->whereNull('completed_at')
                                        ->whereNotNull('sla_target_at')
                                        ->whereRaw('sla_target_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR)'),
            default    => null,
        };
    }
}

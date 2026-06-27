<?php

namespace App\QueryFilters;

/**
 * Filter class for the Driver (DriverProfile) module.
 *
 * Searchable fields: user.name, user.email, nik, license_number
 * Filterable fields: status, license_type
 *
 * Note: DriverProfile is separate from User. The relation is:
 * DriverProfile belongsTo User (via user_id).
 * Most human-readable fields (name, email) live on the User model.
 */
class DriverFilter extends QueryFilter
{
    /**
     * Keyword search across driver identity fields.
     *
     * - user.name + user.email: the human-readable identifiers
     * - nik: national identity number (common in Indonesian logistics)
     * - license_number: SIM number, used for compliance checks
     *
     * Uses whereHas for the User relation to avoid Cartesian product.
     */
    protected function search(string $value): void
    {
        $this->builder->where(function ($q) use ($value) {
            $q->where('nik', 'LIKE', "%{$value}%")
              ->orWhere('license_number', 'LIKE', "%{$value}%")
              ->orWhereHas('user', function ($uq) use ($value) {
                  $uq->where('name', 'LIKE', "%{$value}%")
                     ->orWhere('email', 'LIKE', "%{$value}%");
              });
        });
    }

    /**
     * Filter by driver availability status.
     * Valid values: available, on_trip, inactive.
     */
    protected function status(string $value): void
    {
        $this->builder->where('status', $value);
    }

    /**
     * Filter by driver's license class.
     * Valid values: A, B1, B2.
     * In Indonesian law: B2 can drive heavy trucks, B1 medium trucks, A motorcycles.
     * This filter matters for vehicle-driver matching in shipment assignment.
     */
    protected function license_type(string $value): void
    {
        $this->builder->where('license_type', $value);
    }

    /**
     * Filter drivers whose license is expiring soon or already expired.
     *
     * "expired"    → license_expired_at < TODAY
     * "expiring"   → license_expired_at BETWEEN TODAY AND TODAY + 30 days
     */
    protected function license_status(string $value): void
    {
        match ($value) {
            'expired'  => $this->builder->whereDate('license_expired_at', '<', now()->toDateString()),
            'expiring' => $this->builder->whereBetween('license_expired_at', [
                              now()->toDateString(),
                              now()->addDays(30)->toDateString(),
                          ]),
            default    => null,
        };
    }
}

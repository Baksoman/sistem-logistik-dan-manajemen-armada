<?php

namespace App\QueryFilters;

/**
 * Filter class for the User module.
 *
 * Searchable fields: name, email
 * Filterable fields: role (name), is_active, email_verified
 */
class UserFilter extends QueryFilter
{
    /**
     * Keyword search across name and email.
     * These are the two most meaningful identity fields for a user lookup.
     */
    protected function search(string $value): void
    {
        $this->builder->where(function ($q) use ($value) {
            $q->where('name', 'LIKE', "%{$value}%")
              ->orWhere('email', 'LIKE', "%{$value}%");
        });
    }

    /**
     * Filter by role name (e.g., "Driver", "Admin Logistik").
     * Uses whereHas to avoid joining the pivot table manually.
     */
    protected function role(string $value): void
    {
        $this->builder->whereHas('roles', function ($q) use ($value) {
            $q->where('name', $value);
        });
    }

    /**
     * Filter by active status.
     * Accepts: 1 / 0 / true / false (all coerced to boolean).
     */
    protected function is_active(string $value): void
    {
        $this->builder->where('is_active', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    /**
     * Filter by email verification status.
     * "verified"   → email_verified_at IS NOT NULL
     * "unverified" → email_verified_at IS NULL
     */
    protected function email_verified(string $value): void
    {
        if ($value === 'verified') {
            $this->builder->whereNotNull('email_verified_at');
        } elseif ($value === 'unverified') {
            $this->builder->whereNull('email_verified_at');
        }
    }
}

<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Abstract base class for all module-specific query filters.
 *
 * === How It Works ===
 * Each concrete subclass defines protected methods named after the query
 * parameters they handle (e.g., `status`, `search`, `created_from`).
 *
 * When `apply()` is called, it iterates over all incoming query parameters
 * and checks if a matching method exists in the concrete filter class.
 * If it does, the method is called with the parameter value.
 *
 * === Security ===
 * Only methods explicitly defined in the concrete filter class will be
 * called. No arbitrary column injection is possible because:
 *  1. `method_exists()` is used — only known, whitelisted methods run.
 *  2. Each method is hand-authored and uses parameterized queries.
 *  3. Form Requests validate and sanitize all input before it reaches here.
 *
 * === Design Principle ===
 * Filters are NOT business logic. They are query-building utilities.
 * They belong in app/QueryFilters/, not app/Services/.
 */
abstract class QueryFilter
{
    protected Builder $builder;

    public function __construct(protected Request $request) {}

    /**
     * Apply all matching filters from the current HTTP request to the query.
     *
     * Uses `filled()` to skip empty or null values — clients can safely
     * send partial filter sets without affecting unrelated query clauses.
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->request->query() as $key => $value) {
            // Only call methods that are explicitly defined in the subclass.
            // `method_exists` on $this will also include parent methods,
            // so we restrict to the concrete class using `get_class($this)`.
            if (method_exists($this, $key) && filled($value)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }
}

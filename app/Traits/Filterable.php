<?php

namespace App\Traits;

use App\QueryFilters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Adds a reusable `filter()` Eloquent scope to any model.
 *
 * Usage:
 *   Model::filter($filterInstance)->paginate(15);
 *
 * The filter instance will inspect the current HTTP request and apply
 * only the filter methods that match incoming query parameters.
 */
trait Filterable
{
    /**
     * Apply a QueryFilter instance to the Eloquent query builder.
     *
     * By using a local scope, the filter becomes chainable with any
     * other Eloquent builder calls (where, orderBy, with, etc.).
     */
    public function scopeFilter(Builder $query, QueryFilter $filter): Builder
    {
        return $filter->apply($query);
    }
}

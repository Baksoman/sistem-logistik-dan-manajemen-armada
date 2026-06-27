<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\UserSearchRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\QueryFilters\UserFilter;

/**
 * API endpoint: GET /api/search/users
 *
 * Invokable (single-action) controller. All it does:
 * 1. Authorize via UserPolicy
 * 2. Eager-load relations needed by the Resource
 * 3. Apply filters via the Filterable scope
 * 4. Paginate
 * 5. Return a ResourceCollection
 *
 * Zero business logic lives here. Zero filter logic lives here.
 */
class UserSearchController extends Controller
{
    public function __invoke(UserSearchRequest $request, UserFilter $filter)
    {
        $users = User::with('roles')
            ->filter($filter)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return UserResource::collection($users);
    }
}

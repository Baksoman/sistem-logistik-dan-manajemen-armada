<?php

namespace App\Services;

use App\Models\Warehouse;

class WarehouseService
{
    public function getPaginatedWarehouses($perPage = 10)
    {
        $user = auth()->user();
        $query = Warehouse::latest();
        
        if ($user && !$user->hasRole('Super Admin')) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        
        return $query->paginate($perPage);
    }

    public function createWarehouse(array $data)
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;
        return Warehouse::create($data);
    }

    public function updateWarehouse(Warehouse $warehouse, array $data)
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        return $warehouse->update($data);
    }

    public function deleteWarehouse(Warehouse $warehouse)
    {
        if ($warehouse->stockItems()->count() > 0) {
            throw new \Exception("Cannot delete warehouse. It still has stock items.");
        }
        return $warehouse->delete();
    }
}

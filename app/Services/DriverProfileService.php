<?php

namespace App\Services;

use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DriverProfileService
{
    public function getPaginatedDrivers(int $perPage = 10): LengthAwarePaginator
    {
        return DriverProfile::with('user')->latest()->paginate($perPage);
    }

    public function getAvailableUsersForDriver(): Collection
    {
        return User::all();
    }

    public function createDriverProfile(array $data): DriverProfile
    {
        return DriverProfile::create($data);
    }

    public function updateDriverProfile(DriverProfile $driverProfile, array $data): DriverProfile
    {
        $driverProfile->update($data);
        return $driverProfile;
    }

    public function deleteDriverProfile(DriverProfile $driverProfile): void
    {
        if ($driverProfile->status === 'on_trip') {
            throw new \Exception('Tidak bisa menghapus Driver yang sedang dalam perjalanan (on_trip). Ubah statusnya terlebih dahulu.');
        }
        $driverProfile->delete();
    }
}

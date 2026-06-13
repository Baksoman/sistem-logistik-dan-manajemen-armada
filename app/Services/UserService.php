<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function getPaginatedUsers(int $perPage = 10): LengthAwarePaginator
    {
        return User::with('roles')->latest()->paginate($perPage);
    }

    public function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        if (isset($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            // if we added is_active toggle, we would update it here
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if (isset($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }

        return $user;
    }

    public function deleteUser(User $user): void
    {
        if ($user->hasRole('Super Admin')) {
            throw new \Exception('Super Admin tidak bisa dihapus.');
        }

        if (\App\Models\DriverProfile::where('user_id', $user->id)->exists()) {
            throw new \Exception('User ini terikat sebagai Driver. Hapus data Driver-nya terlebih dahulu.');
        }

        $user->delete();
    }
}

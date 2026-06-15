<?php

namespace App\Services;

use App\Models\Tariff;
use Illuminate\Support\Facades\DB;
use Exception;

class TariffService
{
    /**
     * Create a new Tariff.
     *
     * @param array $data
     * @return Tariff
     */
    public function createTariff(array $data): Tariff
    {
        return DB::transaction(function () use ($data) {
            return Tariff::create($data);
        });
    }

    /**
     * Update an existing Tariff.
     *
     * @param Tariff $tariff
     * @param array $data
     * @return Tariff
     */
    public function updateTariff(Tariff $tariff, array $data): Tariff
    {
        return DB::transaction(function () use ($tariff, $data) {
            $tariff->update($data);
            return $tariff;
        });
    }

    /**
     * Delete a Tariff.
     *
     * @param Tariff $tariff
     * @return void
     */
    public function deleteTariff(Tariff $tariff): void
    {
        DB::transaction(function () use ($tariff) {
            $tariff->delete();
        });
    }
}

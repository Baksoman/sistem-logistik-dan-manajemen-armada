<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_type_id')->constrained()->cascadeOnDelete();
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->decimal('capacity_kg', 10, 2);
            $table->decimal('capacity_volume_cbm', 10, 2);
            $table->decimal('fuel_cost_per_km', 15, 2)->default(0);
            $table->string('fuel_type');
            $table->enum('status', ['available', 'maintenance', 'on_trip', 'inactive'])->default('available');
            $table->date('kir_expired_at')->nullable();
            $table->date('stnk_expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

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
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('shipment_number')->unique();
            $table->foreignUuid('driver_id')->constrained('driver_profiles')->cascadeOnDelete();
            $table->foreignUuid('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('route_version_id')->constrained('route_versions')->cascadeOnDelete();
            $table->enum('status', ['Pending', 'On Process', 'Delivered', 'Failed'])->default('Pending');
            $table->decimal('total_distance_km', 10, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->decimal('cost_per_km', 15, 2)->nullable();
            $table->dateTime('sla_target_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // If route_id is filled, it's a specific tariff for a Master Route
            // If route_id is null, it's the generic formula for Direct Delivery
            $table->foreignUuid('route_id')->nullable()->constrained()->nullOnDelete();
            
            // If vehicle_type_id is filled, the tariff applies to this vehicle type
            $table->foreignUuid('vehicle_type_id')->nullable()->constrained()->nullOnDelete();
            
            // Components for calculation
            $table->decimal('price_per_km', 15, 2)->default(0);
            $table->decimal('price_per_kg', 15, 2)->default(0);
            $table->decimal('price_per_cbm', 15, 2)->default(0);
            
            // Fixed cost base
            $table->decimal('fixed_price', 15, 2)->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};

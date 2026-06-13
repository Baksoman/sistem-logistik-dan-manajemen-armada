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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained('item_categories')->cascadeOnDelete();
            $table->foreignUuid('unit_type_id')->constrained('unit_types')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('upc')->nullable();
            $table->string('brand')->nullable();
            $table->string('name');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('min_quantity', 10, 2)->default(0);
            $table->decimal('weight_kg', 10, 2);
            $table->decimal('volume_cbm', 10, 2);
            $table->foreignUuid('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->foreignUuid('rack_id')->nullable()->constrained('racks')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};

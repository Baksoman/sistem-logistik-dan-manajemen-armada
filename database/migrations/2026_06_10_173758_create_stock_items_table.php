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
            $table->string('gtin')->nullable();
            $table->string('name');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('min_quantity', 10, 2)->default(0);
            $table->decimal('weight_kg', 10, 2);
            $table->decimal('volume_cbm', 10, 2);
            $table->string('zone')->nullable();
            $table->string('bin_location')->nullable();
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

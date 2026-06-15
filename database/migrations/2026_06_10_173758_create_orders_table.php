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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('origin_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->text('destination_address');
            $table->decimal('destination_latitude', 10, 8)->nullable();
            $table->decimal('destination_longitude', 11, 8)->nullable();
            $table->decimal('total_weight', 10, 2)->default(0);
            $table->decimal('total_volume', 10, 2)->default(0);
            $table->decimal('quoted_price', 15, 2)->nullable()->after('total_volume');
            $table->decimal('estimated_distance_km', 10, 2)->nullable()->after('total_volume');
            $table->enum('status', ['Pending Approval', 'Draft', 'Confirmed', 'Assigned', 'In Transit', 'Arrived at Hub', 'Completed', 'Cancelled'])->default('Draft');
            $table->foreignUuid('current_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->string('tracking_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

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
        Schema::create('routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('route_code')->unique();
            $table->enum('route_type', ['land', 'sea', 'combined']);
            $table->string('origin_name');
            $table->string('destination_name');
            $table->decimal('toll_cost', 15, 2)->default(0);
            $table->decimal('ferry_cost', 15, 2)->default(0);
            $table->boolean('is_master')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};

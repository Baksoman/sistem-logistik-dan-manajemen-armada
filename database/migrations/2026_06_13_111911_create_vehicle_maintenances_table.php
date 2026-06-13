<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            
            $table->string('maintenance_type'); // e.g., Ganti Oli, Turun Mesin, Perpanjang KIR
            $table->text('description')->nullable();
            $table->decimal('cost', 15, 2)->nullable(); // Biaya bisa besar, pakai 15 digit
            
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed'])->default('Scheduled');
            
            $table->date('scheduled_date');
            $table->date('completed_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenances');
    }
};
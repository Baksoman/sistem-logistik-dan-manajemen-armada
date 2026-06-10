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
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('nik')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('license_number')->unique();
            $table->enum('license_type', ['A', 'B1', 'B2']);
            $table->date('license_expired_at');
            $table->decimal('rating', 3, 2)->default(0);
            $table->enum('status', ['available', 'on_trip', 'inactive'])->default('available');
            $table->date('joined_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_profiles');
    }
};

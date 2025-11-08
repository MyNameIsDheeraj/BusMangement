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
        Schema::create('bus_charges', function (Blueprint $table) {
            $table->id();
            $table->decimal('base_distance_km', 10, 2);
            $table->decimal('base_charge', 10, 2);
            $table->decimal('per_km_charge', 10, 2);
            $table->string('academic_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_charges');
    }
};
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('admission_no');
            $table->date('dob');
            $table->text('address');
            $table->foreignId('pickup_stop_id')->nullable()->constrained('stops')->onDelete('set null');
            $table->foreignId('drop_stop_id')->nullable()->constrained('stops')->onDelete('set null');
            $table->boolean('bus_service_active')->default(false);
            $table->string('academic_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
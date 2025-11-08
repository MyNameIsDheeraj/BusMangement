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
        // Update buses table to match specification - status should be ENUM
        Schema::table('buses', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
            $table->string('registration_no', 20)->change();
            $table->string('model', 50)->change();
        });
        
        // Update routes table to match specification
        Schema::table('routes', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->decimal('total_kilometer', 5, 2)->change();
            $table->time('start_time')->change();
            $table->time('end_time')->change();
            $table->string('academic_year', 9)->change();
        });
        
        // Update stops table to match specification 
        Schema::table('stops', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->time('pickup_time')->nullable()->change();
            $table->time('drop_time')->nullable()->change();
            $table->decimal('distance_from_start_km', 5, 2)->change();
            $table->string('academic_year', 9)->change();
        });
        
        // Update student_route table to match specification
        Schema::table('student_routes', function (Blueprint $table) {
            $table->enum('type', ['pickup', 'drop'])->change();
            $table->boolean('is_active')->default(true)->change();
            $table->string('academic_year', 9)->change();
        });
        
        // Update payments table to match specification
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->change();
            $table->decimal('total_amount_due', 10, 2)->change();
            $table->string('payment_type', 50)->change();
            $table->enum('status', ['paid', 'pending', 'overdue'])->default('pending')->change();
            $table->date('payment_date')->change();
            $table->string('transaction_id', 100)->nullable()->change();
            $table->string('academic_year', 9)->change();
        });
        
        // Update bus_attendances table to match specification
        Schema::table('bus_attendances', function (Blueprint $table) {
            $table->date('date')->change();
            $table->enum('status', ['present', 'absent', 'late'])->change();
            $table->string('academic_year', 9)->change();
        });
        
        // Update alerts table to match specification
        Schema::table('alerts', function (Blueprint $table) {
            $table->text('description')->change();
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium')->change();
            $table->string('media_path', 255)->nullable()->change();
            $table->enum('status', ['new', 'read', 'resolved'])->default('new')->change();
        });
        
        // Update announcements table to match specification
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('title', 255)->change();
            $table->text('description')->change();
            $table->enum('audience', ['all', 'students', 'parents', 'teachers', 'admin'])->change();
            $table->boolean('is_active')->default(true)->change();
        });
        
        // Update students table to match specification
        Schema::table('students', function (Blueprint $table) {
            $table->string('admission_no', 50)->change();
            $table->string('academic_year', 9)->change();
        });
        

        
        // Update users table to match specification
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 255)->change();
            $table->string('mobile', 15)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Down operations would revert the changes, but for safety we won't implement them
        // since these are fundamental schema changes that shouldn't be reverted in production
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the class_name column and copy data from 'class', then drop 'class' and 'standard'
        Schema::table('classes', function (Blueprint $table) {
            $table->string('class_name')->after('id')->nullable();
        });
        
        // Copy the data from 'class' column to 'class_name'
        DB::statement('UPDATE classes SET class_name = `class`');
        
        // Make class_name non-nullable
        Schema::table('classes', function (Blueprint $table) {
            $table->string('class_name')->nullable(false)->change();
        });
        
        // Drop the old columns
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['class', 'standard']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('class');
            $table->string('standard')->nullable();
            $table->dropColumn('class_name');
        });
    }
};

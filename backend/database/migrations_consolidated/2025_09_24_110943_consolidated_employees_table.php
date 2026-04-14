<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Employees/database/migrations/2025_09_24_110943_create_employees_table.php
        Schema::create('employees', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->nullable()->unique();
                    $table->string('first_name');
                    $table->string('last_name')->nullable();
                    $table->string('job_title')->nullable();
                    $table->string('job_type');
                    $table->date('hire_date');
                    $table->string('phone')->nullable();
                   
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });

        // Source: database/migrations/2025_11_01_085716_add_institute_branch_id_to_employees_table.php
        Schema::table('employees', function (Blueprint $table) {
                    $table->unsignedBigInteger('institute_branch_id')->nullable()->after('user_id');
                    $table->foreign('institute_branch_id')
                          ->references('id')
                          ->on('institute_branches')
                          ->onDelete('set null');
                });

        // Source: Modules/Employees/database/migrations/2025_11_08_095133_add_photo_path_to_employees_table.php
        Schema::table('employees', function (Blueprint $table) {
                    $table->string('photo_path')->nullable();
        
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
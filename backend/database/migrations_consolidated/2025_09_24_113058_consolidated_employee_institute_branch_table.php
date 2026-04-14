<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: database/migrations/2025_09_24_113058_create_employee_institute_branch_table.php
        Schema::create('employee_institute_branch', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                    $table->foreignId('institute_branch_id')->constrained()->onDelete('cascade');
                    $table->timestamps();
                    
                  
                    $table->unique(['employee_id', 'institute_branch_id']);
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_institute_branch');
    }
};
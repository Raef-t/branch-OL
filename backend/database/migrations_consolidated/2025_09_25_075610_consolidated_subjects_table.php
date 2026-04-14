<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Subjects/database/migrations/2025_09_25_075610_create_subjects_table.php
        Schema::create('subjects', function (Blueprint $table) {
                    $table->id();
        
                    // ربط مع academic_branches
                    $table->foreignId('academic_branch_id')
                          ->constrained('academic_branches')
                          ->cascadeOnDelete();
        
                    $table->string('name');
                    $table->text('description')->nullable();
        
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
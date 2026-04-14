<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: database/migrations/2025_09_24_113155_create_instructor_institute_branch_table.php
        Schema::create('instructor_institute_branch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('institute_branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        
            // استخدم اسم قصير للفريد
            $table->unique(['instructor_id', 'institute_branch_id'], 'instructor_branch_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_institute_branch');
    }
};
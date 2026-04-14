<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: database/migrations/2025_09_25_075710_create_instructor_subjects_table.php
        Schema::create('instructor_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        
            $table->unique(['instructor_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_subjects');
    }
};
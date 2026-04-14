<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/BatchStudentSubjects/database/migrations/2025_12_14_085010_create_batch_student_subjects_table.php
        Schema::create('batch_student_subjects', function (Blueprint $table) {
                    $table->id();
        
                    $table->foreignId('batch_student_id')
                        ->constrained('batch_student')
                        ->cascadeOnDelete();
        
                    $table->foreignId('batch_subject_id')
                        ->constrained('batch_subjects')
                        ->cascadeOnDelete();
        
                    $table->string('status')
                        ->default('active')
                        ->comment('active | dropped | completed');
        
                    $table->timestamps();
        
                    // منع تكرار نفس المادة لنفس الطالب
                    $table->unique(['batch_student_id', 'batch_subject_id']);
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_student_subjects');
    }
};
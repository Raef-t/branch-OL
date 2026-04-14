<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/ExamResults/database/migrations/2025_09_25_095041_create_exam_results_table.php
        Schema::create('exam_results', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('exam_id')->constrained();
                    $table->foreignId('student_id')->constrained();
                    $table->decimal('obtained_marks', 5, 2);
                    $table->boolean('is_passed')->nullable(); // يُحسب تلقائيًا
                    $table->text('remarks')->nullable();
                    $table->timestamps();
                });

        // Source: Modules/ExamResults/database/migrations/2026_02_11_072626_add_unique_constraint_to_exam_results_table.php
        Schema::table('exam_results', function (Blueprint $table) {
                    $table->unique(
                        ['exam_id', 'student_id'],
                        'exam_results_exam_id_student_id_unique'
                    );
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
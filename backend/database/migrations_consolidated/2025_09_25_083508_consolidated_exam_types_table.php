<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/ExamTypes/database/migrations/2025_09_25_083508_create_exam_types_table.php
        Schema::create('exam_types', function (Blueprint $table) {
                    $table->id();
                    $table->string('name')->unique(); // مثل: midterm, final, quiz, oral, practical
                    $table->string('description')->nullable(); // تفاصيل اختيارية
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_types');
    }
};
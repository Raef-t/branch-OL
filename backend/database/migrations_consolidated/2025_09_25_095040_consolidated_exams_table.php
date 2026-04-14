<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Exams/database/migrations/2025_09_25_095040_create_exams_table.php
        Schema::create('exams', function (Blueprint $table) {
                    $table->id();
        
                    // الربط مع المادة ضمن الدفعة
                    $table->foreignId('batch_subject_id')
                          ->constrained()
                          ->onDelete('restrict');
        
                    // نوع الامتحان (نظري / عملي / دورة ...)
                    $table->foreignId('exam_type_id')
                          ->constrained('exam_types')
                          ->onDelete('restrict');
        
                    // بيانات الامتحان
                    $table->string('name');
                    $table->date('exam_date');
                    $table->time('exam_time')->nullable();
        
                    // العلامات
                    $table->integer('total_marks');
                    $table->integer('passing_marks');
        
                    // حالة الامتحان
                    $table->enum('status', ['scheduled', 'completed', 'cancelled'])
                          ->default('scheduled');
        
                    // ملاحظات
                    $table->text('remarks')->nullable();
        
                    $table->timestamps();
                });

        // Source: Modules/Exams/database/migrations/2026_01_16_205030_add_exam_end_time_to_exams_table.php
        Schema::table('exams', function (Blueprint $table) {
                    // إضافة العمود الجديد بعد عمود وقت البداية
                    $table->time('exam_end_time')->nullable()->after('exam_time');
                });

        // Source: Modules/Exams/database/migrations/2026_02_15_082950_add_postponed_status_to_exams_table.php
        Schema::table('exams', function (Blueprint $table) {
                    $table->enum('status', [
                        'scheduled',
                        'completed',
                        'cancelled',
                        'postponed'
                    ])
                    ->default('scheduled')
                    ->change();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
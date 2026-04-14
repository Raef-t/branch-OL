<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};

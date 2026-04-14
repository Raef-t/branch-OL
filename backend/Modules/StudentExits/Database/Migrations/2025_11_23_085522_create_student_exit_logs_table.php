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
        Schema::create('student_exit_logs', function (Blueprint $table) {
            $table->id();

            // الطالب
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // اليوم الذي حصل فيه الخروج
            $table->date('exit_date');

            // وقت الخروج
            $table->time('exit_time');

            // وقت العودة إن وُجد (اختياري حالياً)
            $table->time('return_time')->nullable();

            // نوع الخروج (إداري، طبي، ولي أمر، حصة خارجية... إلخ)
            $table->string('exit_type', 50)->nullable();

            // سبب/وصف مختصر للخروج
            $table->string('reason', 255)->nullable();

            // ملاحظة عامة أطول
            $table->text('note')->nullable();

            // الموظف الذي سجّل هذا الخروج
            $table->foreignId('recorded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // فهرس للاستعلامات المتكررة
            $table->index(['student_id', 'exit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_exit_logs');
    }
};

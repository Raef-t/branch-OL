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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // العلاقات
            $table->foreignId('institute_branch_id')
                ->constrained('institute_branches')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('batch_id')
                ->constrained('batches')
                ->cascadeOnDelete();

            // الحقول الأساسية
            $table->date('attendance_date');

            $table->enum('status', ['present', 'absent', 'late'])
                ->default('present');

            $table->foreignId('recorded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // تفاصيل إضافية
            $table->string('device_id', 100)->nullable();
            $table->timestamp('recorded_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

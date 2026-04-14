<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Attendances/database/migrations/2025_10_13_090401_create_attendances_table.php
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

        // Source: Modules/Attendances/database/migrations/2025_11_06_082010_make_recorded_by_nullable_in_attendances_table.php
        Schema::table('attendances', function (Blueprint $table) {
                    $table->foreignId('recorded_by')->nullable()->change();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/ClassSchedules/database/migrations/2025_09_25_100240_create_class_schedules_table.php
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_subject_id')->constrained();
            $table->enum('day_of_week', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'])->nullable();
            $table->date('schedule_date')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_number', 50)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Source: Modules/ClassSchedules/database/migrations/2025_12_21_183211_update_room_and_add_period_to_class_schedules_table.php
        Schema::table('class_schedules', function (Blueprint $table) {
        
                    /*
                     |--------------------------------------------------------------------------
                     | إضافة رقم الحصة (slot)
                     |--------------------------------------------------------------------------
                     | من 1 إلى 5
                     | يحدد ترتيب الحصة داخل اليوم
                     */
                    $table->unsignedTinyInteger('period_number')
                          ->after('day_of_week')
                          ->comment('رقم الحصة في اليوم (1-5)');
        
                    /*
                     |--------------------------------------------------------------------------
                     | استبدال room_number بـ class_room_id
                     |--------------------------------------------------------------------------
                     | nullable: في حال استخدام قاعة BatchSubject الافتراضية
                     */
                    $table->unsignedBigInteger('class_room_id')
                          ->nullable()
                          ->after('end_time');
        
                    // حذف العمود القديم
                    $table->dropColumn('room_number');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};

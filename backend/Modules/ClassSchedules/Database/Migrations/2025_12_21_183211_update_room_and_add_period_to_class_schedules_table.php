<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            $table->foreignId('class_room_id')
                  ->nullable()
                  ->after('end_time')
                  ->constrained('class_rooms')
                  ->nullOnDelete();

            // حذف العمود القديم
            $table->dropColumn('room_number');
        });
    }

    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {

            // إعادة room_number
            $table->string('room_number')
                  ->nullable()
                  ->after('end_time');

            // حذف FK + العمود
            $table->dropForeign(['class_room_id']);
            $table->dropColumn('class_room_id');

            // حذف period_number
            $table->dropColumn('period_number');
        });
    }
};

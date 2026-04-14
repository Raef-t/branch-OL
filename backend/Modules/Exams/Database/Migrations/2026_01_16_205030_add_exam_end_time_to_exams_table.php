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
        Schema::table('exams', function (Blueprint $table) {
            // إضافة العمود الجديد بعد عمود وقت البداية
            $table->time('exam_end_time')->nullable()->after('exam_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // حذف العمود في حال التراجع عن الترحيل
            $table->dropColumn('exam_end_time');
        });
    }
};

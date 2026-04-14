<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // حذف الـ foreign key الحالي
            $table->dropForeign(['bus_id']);

            // إعادة إنشائه مع ON DELETE SET NULL
            $table->foreign('bus_id')
                ->references('id')
                ->on('buses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // حذف المفتاح المعدّل
            $table->dropForeign(['bus_id']);

            // إرجاع السلوك القديم (بدون set null)
            $table->foreign('bus_id')
                ->references('id')
                ->on('buses');
        });
    }
};

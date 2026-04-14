<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {

            // حذف القيود القديمة لتجنب الخطأ
            $table->dropForeign(['institute_branch_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['bus_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['city_id']);

            // إضافة القيود الجديدة مع nullOnDelete
            $table->foreign('institute_branch_id')
                ->references('id')->on('institute_branches')
                ->nullOnDelete();

            $table->foreign('branch_id')
                ->references('id')->on('academic_branches')
                ->nullOnDelete();

            $table->foreign('bus_id')
                ->references('id')->on('buses')
                ->nullOnDelete();

            $table->foreign('status_id')
                ->references('id')->on('student_statuses')
                ->nullOnDelete();

            $table->foreign('city_id')
                ->references('id')->on('cities')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {

            $table->dropForeign(['institute_branch_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['bus_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['city_id']);
        });
    }
};

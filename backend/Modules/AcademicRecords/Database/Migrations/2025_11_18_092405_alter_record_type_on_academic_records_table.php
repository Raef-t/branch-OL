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
        Schema::table('academic_records', function (Blueprint $table) {
            // تغيير نوع الحقل من enum إلى string
            $table->string('record_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_records', function (Blueprint $table) {
            // إعادة الحقل إلى enum (كما كان أصلاً)
            $table->enum('record_type', ['ninth_grade', 'bac_failed', 'bac_passed', 'other'])->change();
        });
    }
};

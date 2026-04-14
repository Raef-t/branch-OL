<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->enum('category', [
                'general',        // رسائل عامة
                'attendance',     // حضور
                'absence',        // غياب
                'behavior',       // سلوك
                'exam',           // امتحانات
                'financial',      // أقساط / دفعات
            ])->after('type')->default('general');
        });
    }

    public function down(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};

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
        Schema::table('students', function (Blueprint $table) {
            // إضافة الحقول الجديدة
            $table->string('health_status')->nullable()->after('notes'); // الحالة الصحية
            $table->string('psychological_status')->nullable()->after('health_status'); // الحالة النفسية
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['health_status', 'psychological_status']);
        });
    }
};

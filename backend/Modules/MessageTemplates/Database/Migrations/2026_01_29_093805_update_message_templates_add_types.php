<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تعديل الـ enum لإضافة الأنواع الجديدة
        DB::statement("ALTER TABLE message_templates MODIFY COLUMN type ENUM('sms', 'in_app', 'email', 'media', 'all')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // الرجوع للقيم القديمة
        DB::statement("ALTER TABLE message_templates MODIFY COLUMN type ENUM('sms', 'in_app', 'email')");
    }
};

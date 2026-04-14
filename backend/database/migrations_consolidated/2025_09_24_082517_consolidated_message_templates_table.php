<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/MessageTemplates/database/migrations/2025_09_24_082517_create_message_templates_table.php
        Schema::create('message_templates', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->enum('type', ['sms', 'in_app', 'email']);
                    $table->string('subject')->nullable();
                    $table->text('body');
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });

        // Source: database/migrations/2026_01_06_113337_add_category_to_message_templates_table.php
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

        // Source: Modules/MessageTemplates/database/migrations/2026_01_29_093805_update_message_templates_add_types.php
        // تعديل الـ enum لإضافة الأنواع الجديدة
                DB::statement("ALTER TABLE message_templates MODIFY COLUMN type ENUM('sms', 'in_app', 'email', 'media', 'all')");
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/FcmTokens/database/migrations/2025_11_04_090646_create_fcm_tokens_table.php
        Schema::create('fcm_tokens', function (Blueprint $table) {
                    $table->id();  // ID أساسي
                    $table->string('token')->unique()->nullable(false);  // التوكن الفريد
                    $table->unsignedBigInteger('user_id')->index()->nullable(false);  // ربط باليوزر (غيره إلى string لو user_id نصي: $table->string('user_id')
                    $table->json('device_info')->nullable();  // JSON nullable بدون default (هنحطه في الكود)
                    $table->timestamp('created_at')->useCurrent();
                    $table->timestamp('last_seen')->useCurrent();
                    $table->timestamp('updated_at')->useCurrent();
        
                    // Foreign key (غيره لو user_id string: $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');)
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
                });

        // Source: Modules/FcmTokens/database/migrations/2025_11_11_100845_alter_token_column_in_fcm_tokens_table.php
        // يجب تحميل مكتبة doctrine/dbal أولاً لتعديل الأعمدة
                // composer require doctrine/dbal
                Schema::table('fcm_tokens', function (Blueprint $table) {
                    // ✅ نحذف القيد unique أولًا لو موجود
                    $table->dropUnique(['token']);
        
                    // ✅ نعدّل العمود من string إلى text مع الحفاظ على البيانات
                    $table->text('token')->change();
                });

        // Source: Modules/FcmTokens/database/migrations/2025_12_25_100339_add_cascade_to_fcm_tokens_to_fcm_tokens_table.php
        Schema::table('fcm_tokens', function (Blueprint $table) {
                    // نحذف المفتاح القديم أولًا
                    $table->dropForeign(['user_id']);
        
                    // نضيف المفتاح الجديد مع onDelete('cascade')
                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
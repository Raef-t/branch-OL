<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // اجعل حقل الايميل قابل لأن يكون NULL
        Schema::table('users', function (Blueprint $table) {
            // تغيير العمود يتطلب doctrine/dbal
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        // اعد العمود ليكون غير قابل للـ NULL كما كان سابقاً
        Schema::table('users', function (Blueprint $table) {
            // افترضنا الطول الافتراضي string(255) كما في Laravel
            $table->string('email', 255)->nullable(false)->change();
        });
    }
};

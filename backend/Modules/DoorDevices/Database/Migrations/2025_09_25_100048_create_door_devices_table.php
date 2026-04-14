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
        Schema::create('door_devices', function (Blueprint $table) {
            $table->id();
            // معرف الجهاز الفريد
            $table->string('device_id')->unique()->comment('معرف فريد للجهاز، مثل DOOR_MAIN_01');
            $table->string('name')->comment('اسم الجهاز لعرضه');
            $table->string('location')->nullable()->comment('موقع الجهاز، مثل المدخل الرئيسي');

            // مفتاح API للمصادقة من الأجهزة
            $table->string('api_key', 64)
                  ->unique()
                  ->comment('مفتاح API للمصادقة من الأجهزة');

            $table->boolean('is_active')->default(true)->comment('هل الجهاز مفعل للتوليد');
            $table->timestamp('last_seen_at')->nullable()->comment('آخر مرة تواصل فيها الجهاز');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('door_devices');
    }
};

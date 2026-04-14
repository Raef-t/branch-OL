<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();

            // اسم المدرسة
            $table->string('name');

            // نوع المدرسة (حكومية - خاصة - أخرى)
            $table->enum('type', ['public', 'private', 'other'])->nullable();

            // المدينة
            $table->string('city')->nullable();

            // ملاحظات إضافية
            $table->text('notes')->nullable();

            // حالة التفعيل
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};

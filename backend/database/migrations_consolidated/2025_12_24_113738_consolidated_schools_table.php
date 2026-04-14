<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Schools/database/migrations/2025_12_24_113738_create_schools_table.php
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
        // Backfill FK after schools table exists.
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'school_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreign('school_id')
                    ->references('id')->on('schools')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};

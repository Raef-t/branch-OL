<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/KnowledgeSources/database/migrations/2025_12_18_113731_create_knowledge_sources_table.php
        Schema::create('knowledge_sources', function (Blueprint $table) {
                    $table->id();
        
                    // اسم طريقة المعرفة (مثلاً: فيسبوك، صديق، إعلان...)
                    $table->string('name')->unique();
        
                    // وصف إضافي (اختياري)
                    $table->text('description')->nullable();
        
                    // حالة التفعيل
                    $table->boolean('is_active')->default(true);
        
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_sources');
    }
};
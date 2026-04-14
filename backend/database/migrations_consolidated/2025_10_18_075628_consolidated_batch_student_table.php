<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/BatchStudents/database/migrations/2025_10_18_075628_create_batch_student_table.php
        Schema::create('batch_student', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('batch_id')->constrained()->onDelete('cascade');
                    $table->foreignId('student_id')->constrained()->onDelete('cascade');
                    
                    $table->timestamps();  // ← هذا يضيف created_at و updated_at تلقائياً
                    
                    $table->unique(['batch_id', 'student_id']);  // الـ unique constraint
                });

        // Source: Modules/BatchStudents/database/migrations/2025_12_14_081515_add_is_partial_to_batch_student_table.php
        Schema::table('batch_student', function (Blueprint $table) {
                    $table->boolean('is_partial')
                        ->default(false)
                        ->comment('false = الطالب مسجل بكامل مواد الدفعة, true = الطالب مسجل بمواد محددة فقط');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_student');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Exams/database/migrations/2026_01_10_105816_create_student_messages_table.php
        Schema::create('student_messages', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('student_id')->constrained()->onDelete('cascade');
                    $table->foreignId('template_id')->nullable()->constrained('message_templates')->onDelete('set null');
                    $table->enum('status', ['sent', 'failed'])->default('sent');
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_messages');
    }
};
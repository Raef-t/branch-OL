<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/StudentStatuses/database/migrations/2025_09_24_082512_create_student_statuses_table.php
        Schema::create('student_statuses', function (Blueprint $table) {
                    $table->id();
                    $table->string('name'); // 'طالب حالي', 'منسحب', ...
                    $table->string('code')->unique(); // 'PRESENT', 'WITHDRAWN', ...
                    $table->text('description')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_statuses');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/AcademicBranches/database/migrations/2025_09_24_082514_create_academic_branches_table.php
        Schema::create('academic_branches', function (Blueprint $table) {
                    $table->id();
                    $table->string('name'); // 'علمي', 'أدبي', 'تاسع'
                    $table->text('description')->nullable();
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_branches');
    }
};
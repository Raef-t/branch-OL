<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Instructors/database/migrations/2025_09_24_112321_create_instructors_table.php
        Schema::create('instructors', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->nullable()->constrained()->unique();
                    $table->string('name');
        
                    $table->string('phone')->nullable();
                    $table->string('specialization')->nullable();
                    $table->date('hire_date');
                    $table->timestamps();
                });

        // Source: Modules/Instructors/database/migrations/2025_11_13_084101_add_profile_photo_to_instructors_table.php
        Schema::table('instructors', function (Blueprint $table) {
                    $table->string('profile_photo_url', 500)
                        ->nullable()
                        ->after('hire_date');
                });

        // Source: Modules/Instructors/database/migrations/2025_12_26_142405_add_institute_branch_id_to_instructors_table.php
        Schema::table('instructors', function (Blueprint $table) {
                    $table
                        ->foreignId('institute_branch_id')
                        ->nullable()
                        ->after('user_id')
                        ->constrained('institute_branches')
                        ->nullOnDelete();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
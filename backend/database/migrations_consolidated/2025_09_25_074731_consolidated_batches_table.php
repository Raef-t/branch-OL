<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Batches/database/migrations/2025_09_25_074731_create_batches_table.php
        Schema::create('batches', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('institute_branch_id')->constrained();
                    $table->string('name');
                    $table->date('start_date');
                    $table->date('end_date');
                    $table->boolean('is_archived')->default(false);
                    $table->boolean('is_hidden')->default(false);
                    $table->boolean('is_completed')->default(false);
                    $table->timestamps();
                });

        // Source: Modules/Batches/database/migrations/2025_10_02_120847_add_academic_branch_id_to_batches_table.php
        Schema::table('batches', function (Blueprint $table) {
                      $table->foreignId('academic_branch_id')
                          ->after('id')
                          ->constrained('academic_branches')
                          ->cascadeOnDelete();
                });

        // Source: Modules/Batches/database/migrations/2025_12_02_100125_add_gender_type_to_batches_table.php
        Schema::table('batches', function (Blueprint $table) {
                    $table->enum('gender_type', ['male', 'female', 'mixed'])->default('mixed');
                });

        // Source: Modules/Batches/database/migrations/2025_12_21_104838_add_class_room_id_to_batches_table.php
        Schema::table('batches', function (Blueprint $table) {
                    $table->unsignedBigInteger('class_room_id')
                          ->nullable()
                          ->after('academic_branch_id');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};

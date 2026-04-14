<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: database/migrations/2025_09_25_094659_create_batch_subjects_table.php
        Schema::create('batch_subjects', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('batch_id')->constrained();
                    $table->foreignId('subject_id')->constrained();
                    $table->foreignId('instructor_subject_id')->constrained(); // ← ربط بالتأهيل
                    $table->foreignId('assigned_by')->constrained('users')->nullable(); // ← من عيّنه
                    $table->date('assignment_date');
                    $table->text('notes')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });

        // Source: database/migrations/2025_11_24_073848_add_class_room_id_to_batch_subject_table.php
        Schema::table('batch_subjects', function (Blueprint $table) {
                    $table->unsignedBigInteger('class_room_id')->nullable()->after('batch_id');
                });

        // Source: database/migrations/2025_12_18_102622_make_nullable_instructor_subject_in_batch_subjects_table.php
        Schema::table('batch_subjects', function (Blueprint $table) {
                    $table->foreignId('instructor_subject_id')
                          ->nullable()
                          ->change(); // السماح بالقيم null
                });

        // Source: Modules/BatchSubjects/database/migrations/2025_12_21_174535_add_nullable_assignment_date_time_to_batch_subjects_table.php
        Schema::table('batch_subjects', function (Blueprint $table) {
                    $table->date('assignment_date')->nullable()->change();
                   
                });

        // Source: Modules/BatchSubjects/database/migrations/2025_12_21_180307_make_assigned_by_nullable_in_batch_subjects_table.php
        Schema::table('batch_subjects', function (Blueprint $table) {
                    $table->foreignId('assigned_by')
                          ->nullable()
                          ->change();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_subjects');
    }
};

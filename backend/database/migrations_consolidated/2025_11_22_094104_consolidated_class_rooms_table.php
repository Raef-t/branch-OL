<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/ClassRooms/database/migrations/2025_11_22_094104_create_class_rooms_table.php
        Schema::create('class_rooms', function (Blueprint $table) {
                    $table->id();
                    $table->string('name'); // اسم القاعة
                    $table->string('code')->nullable(); // رمز القاعة اختياري
                    $table->integer('capacity')->nullable(); // عدد الطلاب الأقصى
                    $table->text('notes')->nullable(); // ملاحظات إضافية
                    $table->timestamps();
                });

        // Source: Modules/ClassRooms/database/migrations/2025_12_27_093025_add_institute_branch_id_to_class_rooms_table.php
        Schema::table('class_rooms', function (Blueprint $table) {
                    $table->unsignedBigInteger('institute_branch_id')
                        ->nullable()
                        ->after('id');
        
                    $table->foreign('institute_branch_id')
                        ->references('id')
                        ->on('institute_branches')
                        ->nullOnDelete();
                });

        // Backfill FK after class_rooms table exists.
        if (Schema::hasTable('batches') && Schema::hasColumn('batches', 'class_room_id')) {
            Schema::table('batches', function (Blueprint $table) {
                $table->foreign('class_room_id')
                    ->references('id')
                    ->on('class_rooms')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('batch_subjects') && Schema::hasColumn('batch_subjects', 'class_room_id')) {
            Schema::table('batch_subjects', function (Blueprint $table) {
                $table->foreign('class_room_id')
                    ->references('id')
                    ->on('class_rooms')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('class_schedules') && Schema::hasColumn('class_schedules', 'class_room_id')) {
            Schema::table('class_schedules', function (Blueprint $table) {
                $table->foreign('class_room_id')
                    ->references('id')
                    ->on('class_rooms')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};

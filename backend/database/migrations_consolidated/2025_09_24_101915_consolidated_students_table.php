<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Students/database/migrations/2025_09_24_101915_create_students_table.php
        Schema::create('students', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('institute_branch_id')->constrained(); 
                    $table->unsignedBigInteger('family_id')->nullable();
        
                    $table->foreignId('user_id')->nullable()->unique(); 
        
                    $table->string('first_name');
                    $table->string('last_name');
                    $table->date('date_of_birth')->nullable();
                    $table->string('birth_place')->nullable();
                    $table->string('profile_photo_url', 500)->nullable();
                    $table->string('id_card_photo_url', 500)->nullable();
        
                    $table->foreignId('branch_id')->nullable()->constrained('academic_branches'); // ← الفرع الأكاديمي
                    $table->date('enrollment_date')->nullable();
                    $table->date('start_attendance_date')->nullable();
        
                    $table->enum('gender', ['male', 'female'])->nullable();
                    $table->string('previous_school_name')->nullable();
                    $table->string('national_id', 200)->nullable();
                    $table->string('how_know_institute')->nullable();
        
                    $table->foreignId('bus_id')->nullable()->constrained();
                    $table->text('notes')->nullable();
        
                    $table->foreignId('status_id')->nullable()->constrained('student_statuses'); 
        
                    $table->foreignId('city_id')->nullable()->constrained();
                    $table->text('qr_code_data')->nullable();
                });

        // Source: database/migrations/2025_09_28_111127_add_timestamps_to_students_table.php
        Schema::table('students', function (Blueprint $table) {
                    $table->timestamps(); // يضيف created_at و updated_at
                });

        // Source: Modules/Students/database/migrations/2025_10_19_085325_add_encrypted_fields_and_hashes_to_students_table.php
        Schema::table('students', function (Blueprint $table) {
                // إضافة أعمدة للهاشات (مطلوبة للبحث)
                $table->string('first_name_hash')->nullable()->index();
                $table->string('last_name_hash')->nullable()->index();
                $table->string('national_id_hash')->nullable()->index();
        
                // إذا لم تكن الحقول الأصلية مشفرة بعد، فاحذفها لاحقًا
                // لكن الآن سنحتفظ بها مؤقتًا للترحيل
            });

        // Source: Modules/Students/database/migrations/2025_11_12_060754_add_health_and_psychological_status_to_students_table.php
        Schema::table('students', function (Blueprint $table) {
                    // إضافة الحقول الجديدة
                    $table->string('health_status')->nullable()->after('notes'); // الحالة الصحية
                    $table->string('psychological_status')->nullable()->after('health_status'); // الحالة النفسية
                });

        // Source: database/migrations/2025_12_29_114733_update_students_bus_fk_on_delete_set_null.php
        Schema::table('students', function (Blueprint $table) {
                    // حذف الـ foreign key الحالي
                    $table->dropForeign(['bus_id']);
        
                    // إعادة إنشائه مع ON DELETE SET NULL
                    $table->foreign('bus_id')
                        ->references('id')
                        ->on('buses')
                        ->nullOnDelete();
                });

        // Source: Modules/Students/database/migrations/2025_12_30_072904_make_students_foreign_columns_nullable.php
        Schema::table('students', function (Blueprint $table) {
                    $table->unsignedBigInteger('institute_branch_id')->nullable()->change();
                    $table->unsignedBigInteger('branch_id')->nullable()->change();
                    $table->unsignedBigInteger('bus_id')->nullable()->change();
                    $table->unsignedBigInteger('status_id')->nullable()->change();
                    $table->unsignedBigInteger('city_id')->nullable()->change();
                });

        // Source: Modules/Students/database/migrations/2025_12_30_072920_alter_students_foreign_keys_nullable_on_delete.php
        Schema::table('students', function (Blueprint $table) {
                    // Replace existing FKs to avoid duplicate-key errors during fresh migration.
                    $table->dropForeign('students_institute_branch_id_foreign');
                    $table->dropForeign('students_branch_id_foreign');
                    $table->dropForeign('students_bus_id_foreign');
                    $table->dropForeign('students_status_id_foreign');
                    $table->dropForeign('students_city_id_foreign');

                    $table->foreign('institute_branch_id')
                        ->references('id')->on('institute_branches')
                        ->nullOnDelete();

                    $table->foreign('branch_id')
                        ->references('id')->on('academic_branches')
                        ->nullOnDelete();

                    $table->foreign('bus_id')
                        ->references('id')->on('buses')
                        ->nullOnDelete();

                    $table->foreign('status_id')
                        ->references('id')->on('student_statuses')
                        ->nullOnDelete();

                    $table->foreign('city_id')
                        ->references('id')->on('cities')
                        ->nullOnDelete();
                });

        // Source: Modules/Students/database/migrations/2026_01_06_190404_add_school_id_to_students_table.php
        Schema::table('students', function (Blueprint $table) {
                    $table->unsignedBigInteger('school_id')->nullable()->after('id');
                });

        // Source: database/migrations/2026_02_18_170255_increase_encrypted_fields_length_in_students_and_guardians.php
        Schema::table('students', function (Blueprint $table) {
                    $table->text('first_name')->change();
                    $table->text('last_name')->change();
                    $table->text('national_id')->nullable()->change();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};


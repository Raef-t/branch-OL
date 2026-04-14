<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/AcademicRecords/database/migrations/2025_09_27_102001_create_academic_records_table.php
        Schema::create('academic_records', function (Blueprint $table) {
                    $table->id();
            
                    $table->foreignId('student_id')->constrained('students');
                    $table->enum('record_type', ['ninth_grade', 'bac_failed', 'bac_passed', 'other']);
                    $table->decimal('total_score', 5, 2)->nullable();
                    $table->year('year')->nullable();
                    $table->text('description')->nullable();
                    
                    $table->timestamps();
                });

        // Source: Modules/AcademicRecords/database/migrations/2025_11_18_092405_alter_record_type_on_academic_records_table.php
        Schema::table('academic_records', function (Blueprint $table) {
                    // تغيير نوع الحقل من enum إلى string
                    $table->string('record_type')->change();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_records');
    }
};
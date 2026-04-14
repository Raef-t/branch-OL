<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

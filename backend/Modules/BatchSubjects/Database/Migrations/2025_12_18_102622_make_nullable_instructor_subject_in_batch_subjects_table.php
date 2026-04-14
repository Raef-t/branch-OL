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
        Schema::table('batch_subjects', function (Blueprint $table) {
            $table->foreignId('instructor_subject_id')
                  ->nullable()
                  ->change(); // السماح بالقيم null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_subjects', function (Blueprint $table) {
            $table->foreignId('instructor_subject_id')
                  ->nullable(false)
                  ->change(); // إعادة عدم السماح بالقيم null
        });
    }
};

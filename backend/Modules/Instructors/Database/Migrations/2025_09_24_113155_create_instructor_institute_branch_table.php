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
      Schema::create('instructor_institute_branch', function (Blueprint $table) {
    $table->id();
    $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
    $table->foreignId('institute_branch_id')->constrained()->onDelete('cascade');
    $table->timestamps();

    // استخدم اسم قصير للفريد
    $table->unique(['instructor_id', 'institute_branch_id'], 'instructor_branch_unique');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_institute_branch');
    }
};

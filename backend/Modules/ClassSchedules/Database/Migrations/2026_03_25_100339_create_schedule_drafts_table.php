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
        Schema::create('schedule_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('draft_group_id')->index()->comment('معرف مجموعة المسودات (للسماح بعدة نسخ)');
            $table->foreignId('batch_subject_id')->constrained('batch_subjects')->cascadeOnDelete();
            $table->enum('day_of_week', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->unsignedTinyInteger('period_number')->comment('رقم الحصة');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->boolean('is_conflict')->default(false)->comment('هل يوجد تعارض في هذه الحصة؟');
            $table->text('conflict_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_drafts');
    }
};

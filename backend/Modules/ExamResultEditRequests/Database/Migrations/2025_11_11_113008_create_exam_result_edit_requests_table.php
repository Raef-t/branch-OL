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
        Schema::create('exam_result_edit_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_result_id');
            $table->unsignedBigInteger('requester_id'); // المستخدم الذي طلب التعديل
            $table->json('original_data'); // البيانات الأصلية كـ JSON
            $table->json('proposed_changes'); // التغييرات المقترحة كـ JSON
            $table->text('reason')->nullable(); // سبب الطلب (اختياري)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            // المفاتيح الخارجية
            $table->foreign('exam_result_id')->references('id')->on('exam_results')->onDelete('cascade');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');

            // فهرس للبحث السريع
            $table->index(['exam_result_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_result_edit_requests');
    }
};
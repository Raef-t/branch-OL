<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/ExamResultEditRequests/database/migrations/2025_11_11_113008_create_exam_result_edit_requests_table.php
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

        // Source: Modules/ExamResultEditRequests/database/migrations/2026_01_04_104621_add_type_to_exam_result_edit_requests_table.php
        Schema::table('exam_result_edit_requests', function (Blueprint $table) {
                    $table->enum('type', ['update', 'delete'])->default('update')->after('id');
                    // أو string إذا بدك مرونة أكبر:
                    // $table->string('type')->default('update')->after('id');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_result_edit_requests');
    }
};
<?php
// database/migrations/xxxx_create_payment_edit_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_edit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade'); // ID المستخدم اللي طلب التعديل
            $table->json('original_data'); // snapshot للبيانات الأصلية (JSON)
            $table->json('proposed_changes'); // التعديلات المقترحة (JSON، fields محددة)
            $table->text('reason')->nullable(); // سبب التعديل (اختياري)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reviewer_comment')->nullable(); // تعليق المدير عند الرفض
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null'); // ID المدير
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_edit_requests');
    }
};
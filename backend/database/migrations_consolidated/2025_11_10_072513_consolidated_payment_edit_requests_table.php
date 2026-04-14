<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/PaymentEditRequests/database/migrations/2025_11_10_072513_create_payment_edit_requests_table.php
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

        // Source: Modules/PaymentEditRequests/database/migrations/2026_01_04_071823_add_action_to_payment_edit_requests_table.php
        Schema::table('payment_edit_requests', function (Blueprint $table) {
                    $table->string('action')
                        ->default('update')
                        ->after('proposed_changes')
                        ->comment('update | delete');
                });

        // Source: Modules/PaymentEditRequests/database/migrations/2026_02_03_092517_make_proposed_changes__null_in_paymentEditRequests_table.php
        Schema::table('payment_edit_requests', function (Blueprint $table) {
                    $table->json('proposed_changes')->nullable()->change();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_edit_requests');
    }
};
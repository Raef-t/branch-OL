<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/PaymentInstallments/database/migrations/2026_01_19_070313_create_payment_installments_table.php
        Schema::create('payment_installments', function (Blueprint $table) {
                    $table->id();
        
                    // العقد المرتبط بالقسط
                    $table->foreignId('enrollment_contract_id')
                        ->constrained('enrollment_contracts')
                        ->cascadeOnDelete();
        
                    $table->unsignedInteger('installment_number');
        
                    $table->date('due_date');
        
                    // المبالغ بالدولار
                    $table->decimal('planned_amount_usd', 10, 2);
                    $table->decimal('paid_amount_usd', 10, 2)->default(0);
        
                    // سعر الصرف والمبلغ بالليرة (قد يكون غير معروف عند الإنشاء)
                    $table->decimal('exchange_rate_at_due_date', 10, 4)->nullable();
                    $table->decimal('planned_amount_syp', 12, 2)->nullable();
        
                    $table->enum('status', [
                        'pending',
                        'paid',
                        'overdue',
                        'skipped'
                    ])->default('pending');
        
                    $table->timestamps();
        
                    // فهارس مفيدة للأداء
                    $table->index(['enrollment_contract_id', 'status']);
                });

        // Source: Modules/PaymentInstallments/database/migrations/2026_02_05_080450_encrypt_fields_in_payment_installments_table.php
        Schema::table('payment_installments', function (Blueprint $table) {
                    // تحويل الأعمدة إلى نص لتخزين القيم المشفرة
                    $table->text('planned_amount_usd')->nullable()->change();
                    $table->text('planned_amount_syp')->nullable()->change();
                    $table->text('paid_amount_usd')->nullable()->change();
        
                    // إضافة أعمدة hash لكل حقل
                    $table->string('planned_amount_usd_hash', 64)->nullable()->after('planned_amount_usd');
                    $table->string('planned_amount_syp_hash', 64)->nullable()->after('planned_amount_syp');
                    $table->string('paid_amount_usd_hash', 64)->nullable()->after('paid_amount_usd');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_installments');
    }
};
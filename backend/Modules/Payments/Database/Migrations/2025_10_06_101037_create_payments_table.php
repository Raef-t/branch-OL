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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // حقل رقم الإيصال الفريد
            $table->string('receipt_number')->unique();

            // الحقول الأساسية
            $table->foreignId('institute_branch_id')
                ->constrained('institute_branches')
                ->cascadeOnDelete();

            $table->foreignId('enrollment_contract_id')
                ->constrained('enrollment_contracts')
                ->cascadeOnDelete();

            $table->foreignId('payment_installments_id')
                ->nullable()
                ->constrained('payment_installments')
                ->nullOnDelete();

            $table->decimal('amount_usd', 10, 2);
            $table->decimal('amount_syp', 12, 2);
            $table->decimal('exchange_rate_at_payment', 10, 4);
            $table->enum('currency', ['USD', 'SYP'])->default('SYP');
            $table->date('due_date');
            $table->date('paid_date');
            $table->text('description')->nullable();

            $table->timestamps();

            // Indexes للأداء (اختياري)
            // $table->index(['student_id', 'contract_id']);
            // $table->index('installment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

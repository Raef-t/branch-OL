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
        if (!Schema::hasTable('payment_installments')) {
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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_installments');
    }
};

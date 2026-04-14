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
        Schema::table('payments', function (Blueprint $table) {
            // حذف الحقول المطلوبة
            if (Schema::hasColumn('payments', 'payment_installments_id')) {
                $table->dropForeign(['payment_installments_id']); // حذف الـ FK أولاً
                $table->dropColumn('payment_installments_id');
            }

            if (Schema::hasColumn('payments', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // إعادة إضافة الحقول في حالة rollback
            $table->foreignId('payment_installments_id')
                ->nullable()
                ->constrained('payment_installments')
                ->nullOnDelete();

            $table->date('due_date');
        });
    }
};

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
            // حذف الـ foreign key أولًا
            $table->dropForeign(['payment_installments_id']);

            // ثم حذف العمود
            $table->dropColumn('payment_installments_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // إعادة العمود
            $table->unsignedBigInteger('payment_installments_id')->nullable();

            // إعادة الـ foreign key
            $table->foreign('payment_installments_id')->references('id')->on('payment_installments')->onDelete('cascade');
        });
    }
};

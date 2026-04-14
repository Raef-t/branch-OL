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
            // اجعل الحقول تقبل القيم الفارغة
            $table->decimal('amount_usd', 10, 2)->nullable()->change();
            $table->decimal('amount_syp', 12, 2)->nullable()->change();
            $table->decimal('exchange_rate_at_payment', 10, 4)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // ارجعها إلزامية إذا رجعنا الوراء
            $table->decimal('amount_usd', 10, 2)->nullable(false)->change();
            $table->decimal('amount_syp', 12, 2)->nullable(false)->change();
            $table->decimal('exchange_rate_at_payment', 10, 4)->nullable(false)->change();
        });
    }
};

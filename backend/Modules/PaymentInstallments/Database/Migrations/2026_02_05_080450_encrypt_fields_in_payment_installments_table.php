<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
        Schema::table('payment_installments', function (Blueprint $table) {
            // إعادة الأعمدة إلى decimal
            $table->decimal('planned_amount_usd', 10, 2)->change();
            $table->decimal('planned_amount_syp', 12, 2)->nullable()->change();
            $table->decimal('paid_amount_usd', 10, 2)->default(0)->change();

            // حذف أعمدة hash
            $table->dropColumn([
                'planned_amount_usd_hash',
                'planned_amount_syp_hash',
                'paid_amount_usd_hash'
            ]);
        });
    }
};

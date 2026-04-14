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
            // تغيير الأعمدة إلى نصوص لتخزين القيم المشفرة
            $table->text('amount_usd')->nullable()->change();
            $table->text('amount_syp')->nullable()->change();

            // إضافة أعمدة hash جديدة
            $table->string('amount_usd_hash', 64)->nullable()->after('amount_usd');
            $table->string('amount_syp_hash', 64)->nullable()->after('amount_syp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // إعادة الأعمدة إلى decimal
            $table->decimal('amount_usd', 10, 2)->nullable()->change();
            $table->decimal('amount_syp', 12, 2)->nullable()->change();

            // حذف أعمدة hash
            $table->dropColumn(['amount_usd_hash', 'amount_syp_hash']);
        });
    }
};

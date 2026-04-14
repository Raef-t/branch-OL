<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            // تعديل الأعمدة الموجودة لتصبح نصوص لتخزين التشفير
            $table->text('total_amount_usd')->nullable()->change();
            $table->text('final_amount_usd')->nullable()->change();
            $table->text('paid_amount_usd')->nullable()->change();
            $table->text('final_amount_syp')->nullable()->change();

            // إضافة أعمدة hash جديدة
            $table->string('total_amount_usd_hash', 64)->nullable()->after('total_amount_usd');
            $table->string('final_amount_usd_hash', 64)->nullable()->after('final_amount_usd');
            $table->string('paid_amount_usd_hash', 64)->nullable()->after('paid_amount_usd');
            $table->string('final_amount_syp_hash', 64)->nullable()->after('final_amount_syp');
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            // إعادة الأعمدة إلى decimal
            $table->decimal('total_amount_usd', 10, 2)->nullable()->change();
            $table->decimal('final_amount_usd', 10, 2)->nullable()->change();
            $table->decimal('paid_amount_usd', 10, 2)->nullable()->change();
            $table->decimal('final_amount_syp', 12, 2)->nullable()->change();

            // حذف أعمدة hash
            $table->dropColumn([
                'total_amount_usd_hash',
                'final_amount_usd_hash',
                'paid_amount_usd_hash',
                'final_amount_syp_hash'
            ]);
        });
    }
};

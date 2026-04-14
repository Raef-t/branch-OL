<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'down_payment_usd',
                'down_payment_syp',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->decimal('down_payment_usd', 10, 2)->nullable()->after('final_amount_usd');
            $table->unsignedBigInteger('down_payment_syp')->nullable()->after('down_payment_usd');
        });
    }
};

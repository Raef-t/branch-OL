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
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->decimal('paid_amount_usd', 10, 2)->default(0)->after('final_amount_usd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->dropColumn('paid_amount_usd');
        });
    }
};

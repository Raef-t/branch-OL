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
            $table->unsignedBigInteger('down_payment_syp')->nullable()->after('down_payment_usd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            
        });
    }
};

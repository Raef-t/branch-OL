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
            $table->text('discount_amount')->nullable()->after('discount_percentage');
            $table->string('discount_amount_hash', 64)->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_amount_hash']);
        });
    }
};

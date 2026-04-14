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
            $table->text('discount_percentage')->nullable()->change();
            $table->string('discount_percentage_hash', 64)->nullable()->after('discount_percentage');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->nullable()->change();
            $table->dropColumn('discount_percentage_hash');

        });
    }
};

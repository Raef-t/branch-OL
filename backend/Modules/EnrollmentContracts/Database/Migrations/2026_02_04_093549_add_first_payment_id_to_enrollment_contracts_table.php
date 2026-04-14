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
            $table->foreignId('first_payment_id')
                ->nullable()
                ->after('is_active')
                ->constrained('payments')
                ->nullOnDelete()
                ->comment('يحوي ID الدفعة الأولى إذا موجودة، أو null إذا لا توجد دفعة أولى');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->dropForeign(['first_payment_id']);
            $table->dropColumn('first_payment_id');
        });
    }
};

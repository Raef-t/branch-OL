<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollment_contracts', 'installments_count')) {
                $table->unsignedInteger('installments_count')->nullable()->after('mode');
            }
            if (!Schema::hasColumn('enrollment_contracts', 'down_payment_usd')) {
                $table->decimal('down_payment_usd', 12, 2)->default(0)->after('installments_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('enrollment_contracts', 'installments_count')) {
                $table->dropColumn('installments_count');
            }
            if (Schema::hasColumn('enrollment_contracts', 'down_payment_usd')) {
                $table->dropColumn('down_payment_usd');
            }
        });
    }
};

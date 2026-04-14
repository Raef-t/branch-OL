<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->date('installments_start_date')->nullable()->after('agreed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrollment_contracts', function (Blueprint $table) {
            $table->dropColumn('installments_start_date');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_edit_requests', function (Blueprint $table) {
            $table->string('action')
                ->default('update')
                ->after('proposed_changes')
                ->comment('update | delete');
        });
    }

    public function down(): void
    {
        Schema::table('payment_edit_requests', function (Blueprint $table) {
            $table->dropColumn('action');
        });
    }
};

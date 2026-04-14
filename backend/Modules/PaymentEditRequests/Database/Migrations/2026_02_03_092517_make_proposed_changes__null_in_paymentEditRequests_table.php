<?php
// database/migrations/xxxx_update_proposed_changes_nullable.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_edit_requests', function (Blueprint $table) {
            $table->json('proposed_changes')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('payment_edit_requests', function (Blueprint $table) {
            $table->json('proposed_changes')->nullable(false)->change();
        });
    }
};

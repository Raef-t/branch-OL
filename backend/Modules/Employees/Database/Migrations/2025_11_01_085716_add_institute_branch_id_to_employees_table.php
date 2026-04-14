<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_branch_id')->nullable()->after('user_id');
            $table->foreign('institute_branch_id')
                  ->references('id')
                  ->on('institute_branches')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['institute_branch_id']);
            $table->dropColumn('institute_branch_id');
        });
    }
};
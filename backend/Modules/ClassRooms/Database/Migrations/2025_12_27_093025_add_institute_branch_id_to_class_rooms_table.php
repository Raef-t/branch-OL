<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_branch_id')
                ->nullable()
                ->after('id');

            $table->foreign('institute_branch_id')
                ->references('id')
                ->on('institute_branches')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->dropForeign(['institute_branch_id']);
            $table->dropColumn('institute_branch_id');
        });
    }

};

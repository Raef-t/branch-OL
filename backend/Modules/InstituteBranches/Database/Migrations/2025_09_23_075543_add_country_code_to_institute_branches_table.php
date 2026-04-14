<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('institute_branches', function (Blueprint $table) {
            $table->string('country_code', 5)->nullable()->comment('رمز الهاتف الدولي للدولة، مثل: +963');
        });
    }

    public function down()
    {
        Schema::table('institute_branches', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }
};
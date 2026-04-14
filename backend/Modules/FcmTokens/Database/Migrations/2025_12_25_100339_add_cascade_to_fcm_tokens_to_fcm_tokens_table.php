<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // نحذف المفتاح القديم أولًا
            $table->dropForeign(['user_id']);

            // نضيف المفتاح الجديد مع onDelete('cascade')
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            // نرجع المفتاح كما كان بدون cascade
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
        });
    }
};

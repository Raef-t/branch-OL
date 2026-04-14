<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();  // ID أساسي
            $table->string('token')->unique()->nullable(false);  // التوكن الفريد
            $table->unsignedBigInteger('user_id')->index()->nullable(false);  // ربط باليوزر (غيره إلى string لو user_id نصي: $table->string('user_id')
            $table->json('device_info')->nullable();  // JSON nullable بدون default (هنحطه في الكود)
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('last_seen')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // Foreign key (غيره لو user_id string: $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
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
        Schema::create('door_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('door_devices');
            $table->string('session_token')->unique();
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->foreignId('student_id')->nullable()->constrained();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('door_sessions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/DoorSessions/database/migrations/2025_09_25_100049_create_door_sessions_table.php
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

    public function down(): void
    {
        Schema::dropIfExists('door_sessions');
    }
};
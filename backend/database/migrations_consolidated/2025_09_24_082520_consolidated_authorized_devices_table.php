<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/AuthorizedDevices/database/migrations/2025_09_24_082520_create_authorized_devices_table.php
        Schema::create('authorized_devices', function (Blueprint $table) {
                    $table->id();
                    $table->string('device_id')->unique(); // UUID, IMEI, etc.
                    $table->string('device_name')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamp('last_used_at')->nullable();
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorized_devices');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Settings/database/migrations/2025_11_19_080015_create_settings_table.php
        Schema::create('settings', function (Blueprint $table) {
                    $table->id();
                    $table->boolean('is_system_enabled')->default(true);
                    $table->timestamps();
                });

        // Source: Modules/Settings/database/migrations/2025_11_19_081424_add_maintenance_message_to_settings_table.php
        Schema::table('settings', function (Blueprint $table) {
                    $table->string('maintenance_message', 500)
                          ->nullable()
                          ->after('is_system_enabled');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
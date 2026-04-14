<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Buses/database/migrations/2025_09_24_101546_create_buses_table.php
        Schema::create('buses', function (Blueprint $table) {
                    $table->id();
                    $table->string('name', 100);
                    $table->integer('capacity')->nullable();
                    $table->string('driver_name', 255)->nullable();
                    $table->text('route_description')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
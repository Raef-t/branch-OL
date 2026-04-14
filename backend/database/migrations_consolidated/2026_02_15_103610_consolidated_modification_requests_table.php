<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/ModificationRequests/database/migrations/2026_02_15_103610_create_modification_requests_table.php
        Schema::create('modification_requests', function (Blueprint $table) {
                    $table->id();
                    
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('modification_requests');
    }
};
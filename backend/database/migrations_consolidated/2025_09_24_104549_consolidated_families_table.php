<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Families/database/migrations/2025_09_24_104549_create_families_table.php
        Schema::create('families', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->nullable()->unique(); 
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: database/migrations/0001_01_01_000000_create_users_table.php
        Schema::create('password_reset_tokens', function (Blueprint $table) {
                    $table->string('email')->primary();
                    $table->string('token');
                    $table->timestamp('created_at')->nullable();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
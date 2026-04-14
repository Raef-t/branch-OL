<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: database/migrations/2025_11_20_074331_create_scheduled_tasks_table.php
        Schema::create('scheduled_tasks', function (Blueprint $table) {
                    $table->id();
                    $table->string('task_name')->unique(); // مثل 'check-installment-delays'
                    $table->timestamp('last_run_at')->nullable();
                    $table->timestamps();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }
};
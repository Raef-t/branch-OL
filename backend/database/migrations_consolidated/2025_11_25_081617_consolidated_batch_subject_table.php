<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/BatchSubjects/database/migrations/2025_11_25_081617_add_employee_id_to_batch_subject_table.php
        Schema::table('batch_subject', function (Blueprint $table) {
                    
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_subject');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table
                ->foreignId('institute_branch_id')
                ->nullable()
                ->after('user_id')
                ->constrained('institute_branches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropForeign(['institute_branch_id']);
            $table->dropColumn('institute_branch_id');
        });
    }
};

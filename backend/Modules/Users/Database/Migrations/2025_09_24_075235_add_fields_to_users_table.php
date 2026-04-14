<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('unique_id')->unique()->after('id');
            $table->enum('role', ['admin', 'staff', 'student', 'family'])->after('password');
            $table->boolean('is_approved')->default(true)->after('role');
            $table->boolean('force_password_change')->default(false)->after('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['unique_id', 'role', 'is_approved', 'force_password_change']);
        });
    }
};

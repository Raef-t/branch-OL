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
        Schema::table('students', function (Blueprint $table) {
            $table->text('first_name')->change();
            $table->text('last_name')->change();
            $table->text('national_id')->nullable()->change();
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->text('first_name')->change();
            $table->text('last_name')->change();
            $table->text('national_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name', 255)->change();
            $table->string('last_name', 255)->change();
            $table->string('national_id', 200)->nullable()->change();
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->string('first_name', 255)->change();
            $table->string('last_name', 255)->change();
            $table->string('national_id', 200)->nullable()->change();
        });
    }
};

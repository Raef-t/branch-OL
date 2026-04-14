<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_branch_id')->nullable()->change();
            $table->unsignedBigInteger('branch_id')->nullable()->change();
            $table->unsignedBigInteger('bus_id')->nullable()->change();
            $table->unsignedBigInteger('status_id')->nullable()->change();
            $table->unsignedBigInteger('city_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_branch_id')->nullable(false)->change();
            $table->unsignedBigInteger('branch_id')->nullable(false)->change();
            $table->unsignedBigInteger('bus_id')->nullable(false)->change();
            $table->unsignedBigInteger('status_id')->nullable(false)->change();
            $table->unsignedBigInteger('city_id')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/InstituteBranches/database/migrations/2025_09_18_114623_create_institute_branches_table.php
        Schema::create('institute_branches', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('code')->unique();
                    $table->text('address')->nullable();
                    $table->string('phone')->nullable();
                    $table->string('email')->nullable();
                    $table->string('manager_name')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });

        // Source: Modules/InstituteBranches/database/migrations/2025_09_23_075543_add_country_code_to_institute_branches_table.php
        Schema::table('institute_branches', function (Blueprint $table) {
                    $table->string('country_code', 5)->nullable()->comment('رمز الهاتف الدولي للدولة، مثل: +963');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_branches');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Guardians/database/migrations/2025_09_24_103357_create_guardians_table.php
        Schema::create('guardians', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('family_id')->nullable();
                    $table->string('first_name');
                    $table->string('last_name');
                    $table->string('national_id', 200)->nullable(); // ← الرقم الوطني
                    $table->string('phone', 20)->nullable();
                    $table->boolean('is_primary_contact')->default(false);
                    $table->string('occupation')->nullable(); 
                    $table->text('address')->nullable(); 
                    $table->enum('relationship', ['father', 'mother', 'legal_guardian', 'other'])->nullable(); 
                    $table->timestamps();
                });

        // Source: Modules/Guardians/database/migrations/2025_10_18_103408_update_guardians_table_for_encryption.php
        Schema::table('guardians', function (Blueprint $table) {
                    // أعمدة الهاش للبحث (لأننا نُشفّر الأسماء والرقم الوطني) 
                    $table->string('first_name_hash')->nullable()->index();
                    $table->string('last_name_hash')->nullable()->index();
                    $table->string('national_id_hash')->nullable()->index();
        
                    // لا نلمس عمود phone — نبقيه كما هو (لكن لا نستخدمه)
                });

        // Source: database/migrations/2026_02_18_170255_increase_encrypted_fields_length_in_students_and_guardians.php
        Schema::table('guardians', function (Blueprint $table) {
                    $table->text('first_name')->change();
                    $table->text('last_name')->change();
                    $table->text('national_id')->nullable()->change();
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
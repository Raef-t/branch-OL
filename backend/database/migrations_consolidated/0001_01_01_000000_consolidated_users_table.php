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
        Schema::create('users', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email')->unique();
                    $table->timestamp('email_verified_at')->nullable();
                    $table->string('password');
                    $table->rememberToken();
                    $table->timestamps();
                });

        // Source: Modules/Users/database/migrations/2025_09_24_075235_add_fields_to_users_table.php
        Schema::table('users', function (Blueprint $table) {
                    $table->string('unique_id')->unique()->after('id');
                    $table->enum('role', ['admin', 'staff', 'student', 'family'])->after('password');
                    $table->boolean('is_approved')->default(true)->after('role');
                    $table->boolean('force_password_change')->default(false)->after('is_approved');
                });

        // Source: Modules/Users/database/migrations/2025_09_27_093248_add_name_and_password_to_users_table.php
        Schema::table('users', function (Blueprint $table) {
                    $table->string('name')->nullable()->after('role')->change();
                    $table->string('password')->nullable()->after('name')->change();
                });

        // Source: Modules/Users/database/migrations/2025_09_28_091350_make_email_nullable_in_users_table.php
        // اجعل حقل الايميل قابل لأن يكون NULL
                Schema::table('users', function (Blueprint $table) {
                    // تغيير العمود يتطلب doctrine/dbal
                    $table->string('email')->nullable()->change();
                });

        // Source: Modules/Users/database/migrations/2025_10_20_090409_update_role_enum_in_users_table.php
        // تعديل العمود role لاستبدال staff بـ employee
                DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'employee', 'student', 'family') AFTER password");

        // Source: Modules/Users/database/migrations/2025_10_21_075632_add_fcm_token_to_users_table.php
        Schema::table('users', function (Blueprint $table) {
                    $table->string('fcm_token', 255)->nullable()->unique()->after('email'); 
                });

        // Source: Modules/Users/database/migrations/2025_11_04_091625_remove_fcm_token_from_users_table.php
        Schema::table('users', function (Blueprint $table) {
                    // حذف حقل FCM Token
                    $table->dropColumn('fcm_token');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
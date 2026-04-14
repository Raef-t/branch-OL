<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تعديل العمود role لاستبدال staff بـ employee
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'employee', 'student', 'family') AFTER password");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة القيمة القديمة staff بدلاً من employee
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'staff', 'student', 'family') AFTER password");
    }
};

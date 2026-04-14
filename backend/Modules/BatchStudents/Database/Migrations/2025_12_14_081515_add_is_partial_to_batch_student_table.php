<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batch_student', function (Blueprint $table) {
            $table->boolean('is_partial')
                ->default(false)
                ->comment('false = الطالب مسجل بكامل مواد الدفعة, true = الطالب مسجل بمواد محددة فقط');
        });
    }

    public function down(): void
    {
        Schema::table('batch_student', function (Blueprint $table) {
            $table->dropColumn('is_partial');
        });
    }
};

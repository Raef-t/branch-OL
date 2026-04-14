<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_result_edit_requests', function (Blueprint $table) {
            $table->enum('type', ['update', 'delete'])->default('update')->after('id');
            // أو string إذا بدك مرونة أكبر:
            // $table->string('type')->default('update')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('exam_result_edit_requests', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
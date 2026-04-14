<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   
public function up()
{
    Schema::table('students', function (Blueprint $table) {
        // إضافة أعمدة للهاشات (مطلوبة للبحث)
        $table->string('first_name_hash')->nullable()->index();
        $table->string('last_name_hash')->nullable()->index();
        $table->string('national_id_hash')->nullable()->index();

        // إذا لم تكن الحقول الأصلية مشفرة بعد، فاحذفها لاحقًا
        // لكن الآن سنحتفظ بها مؤقتًا للترحيل
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn(['first_name_hash', 'last_name_hash', 'national_id_hash']);
    });
}
};

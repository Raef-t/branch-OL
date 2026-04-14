<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // يجب تحميل مكتبة doctrine/dbal أولاً لتعديل الأعمدة
        // composer require doctrine/dbal
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // ✅ نحذف القيد unique أولًا لو موجود
            $table->dropUnique(['token']);

            // ✅ نعدّل العمود من string إلى text مع الحفاظ على البيانات
            $table->text('token')->change();
        });
    }

    public function down()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // نرجعه كما كان (string 255)
            $table->string('token', 255)->unique()->change();
        });
    }
};

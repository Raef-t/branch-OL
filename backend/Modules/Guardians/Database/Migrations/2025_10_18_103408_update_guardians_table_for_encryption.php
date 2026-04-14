<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('guardians', function (Blueprint $table) {
            // أعمدة الهاش للبحث (لأننا نُشفّر الأسماء والرقم الوطني) 
            $table->string('first_name_hash')->nullable()->index();
            $table->string('last_name_hash')->nullable()->index();
            $table->string('national_id_hash')->nullable()->index();

            // لا نلمس عمود phone — نبقيه كما هو (لكن لا نستخدمه)
        });
    }

    public function down()
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropColumn(['first_name_hash', 'last_name_hash', 'national_id_hash']);
        });
    }
};
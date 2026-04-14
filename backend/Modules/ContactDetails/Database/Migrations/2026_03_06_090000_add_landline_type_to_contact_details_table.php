<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة نوع "landline" (هاتف أرضي) إلى حقل type في جدول contact_details.
     * عند اختيار landline يتم ربط السجل بالعائلة (family_id) إجباريًا.
     */
    public function up(): void
    {
        // تعديل حقل enum لإضافة landline
        DB::statement("ALTER TABLE contact_details MODIFY COLUMN `type` ENUM('phone','email','address','whatsapp','landline') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع enum للقيم الأصلية (تأكد من عدم وجود سجلات landline قبل الـ rollback)
        DB::statement("ALTER TABLE contact_details MODIFY COLUMN `type` ENUM('phone','email','address','whatsapp') NOT NULL");
    }
};

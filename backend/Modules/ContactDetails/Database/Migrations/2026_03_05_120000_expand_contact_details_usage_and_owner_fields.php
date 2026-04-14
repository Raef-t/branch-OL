<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_details', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('guardian_id')->constrained('students')->nullOnDelete();
            $table->foreignId('family_id')->nullable()->after('student_id')->constrained('families')->nullOnDelete();

            $table->string('owner_type', 20)->nullable()->after('phone_number');
            $table->string('owner_name', 100)->nullable()->after('owner_type');

            $table->boolean('supports_call')->default(true)->after('owner_name');
            $table->boolean('supports_whatsapp')->default(false)->after('supports_call');
            $table->boolean('supports_sms')->default(false)->after('supports_whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('contact_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
            $table->dropConstrainedForeignId('family_id');

            $table->dropColumn([
                'owner_type',
                'owner_name',
                'supports_call',
                'supports_whatsapp',
                'supports_sms',
            ]);
        });
    }
};

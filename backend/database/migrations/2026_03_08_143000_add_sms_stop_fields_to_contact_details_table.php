<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumns('contact_details', ['is_sms_stopped', 'stop_sms_from', 'stop_sms_to'])) {
            Schema::table('contact_details', function (Blueprint $table) {
                $table->boolean('is_sms_stopped')->default(false)->after('supports_sms');
                $table->date('stop_sms_from')->nullable()->after('is_sms_stopped');
                $table->date('stop_sms_to')->nullable()->after('stop_sms_from');
            });
        }
    }

    public function down(): void
    {
        Schema::table('contact_details', function (Blueprint $table) {
            $table->dropColumn(['is_sms_stopped', 'stop_sms_from', 'stop_sms_to']);
        });
    }
};

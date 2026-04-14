<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Notifications/database/migrations/2026_02_01_195756_create_notifications_table.php
        Schema::create('notifications', function (Blueprint $table) {
                    $table->id();
        
                    $table->string('title');
                    $table->text('body');
        
                    // قالب الرسالة (اختياري)
                    $table->foreignId('template_id')
                        ->nullable()
                        ->constrained('message_templates')
                        ->nullOnDelete();
        
                    // المرسل (admin أو system)
                    $table->foreignId('sender_id')
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();
        
                    $table->string('sender_type')->nullable(); // admin | system
        
                    $table->timestamps();
                });

        // Source: Modules/Notifications/database/migrations/2026_02_01_232332_add_target_snapshot_to_notifications_table.php
        Schema::table('notifications', function (Blueprint $table) {
                    $table->json('target_snapshot')->nullable()->after('sender_type');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
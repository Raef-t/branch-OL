<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/Payments/database/migrations/2025_10_06_101037_create_payments_table.php
        Schema::create('payments', function (Blueprint $table) {
                    $table->id();
        
                    // حقل رقم الإيصال الفريد
                    $table->string('receipt_number')->unique();
        
                    // الحقول الأساسية
                    $table->foreignId('institute_branch_id')
                        ->constrained('institute_branches')
                        ->cascadeOnDelete();
        
                    $table->foreignId('enrollment_contract_id')
                        ->constrained('enrollment_contracts')
                        ->cascadeOnDelete();
        
                    $table->unsignedBigInteger('payment_installments_id')->nullable();
        
                    $table->decimal('amount_usd', 10, 2);
                    $table->decimal('amount_syp', 12, 2);
                    $table->decimal('exchange_rate_at_payment', 10, 4);
                    $table->enum('currency', ['USD', 'SYP'])->default('SYP');
                    $table->date('due_date');
                    $table->date('paid_date');
                    $table->text('description')->nullable();
        
                    $table->timestamps();
        
                    // Indexes للأداء (اختياري)
                    // $table->index(['student_id', 'contract_id']);
                    // $table->index('installment_id');
                });

        // Source: Modules/Payments/database/migrations/2025_12_04_084809_add_reason_column_to_payments_table.php
        Schema::table('payments', function (Blueprint $table) {
                    $table->text('reason')->nullable()->after('description');
                });

        // Source: Modules/Payments/database/migrations/2025_12_16_102236_rename_description_to_note_in_payments_table.php
        Schema::table('payments', function (Blueprint $table) {
                    $table->renameColumn('description', 'note');
                });

        // Source: Modules/Payments/database/migrations/2025_12_16_111756_rename_note_to_description_in_payments_table.php
        Schema::table('payments', function (Blueprint $table) {
                    $table->renameColumn('note', 'description');
                });

        // Source: Modules/Payments/database/migrations/2025_12_21_104538_remove_payment_installments_id_from_payments_table.php
        Schema::table('payments', function (Blueprint $table) {
                    // حذف الـ foreign key أولًا
        
                    // ثم حذف العمود
                    $table->dropColumn('payment_installments_id');
                });

        // Source: Modules/Payments/database/migrations/2026_01_03_180413_remove_fields_from_payments_table.php
        Schema::table('payments', function (Blueprint $table) {
                    // حذف الحقول المطلوبة
                    if (Schema::hasColumn('payments', 'payment_installments_id')) {
                        $table->dropColumn('payment_installments_id');
                    }
        
                    if (Schema::hasColumn('payments', 'due_date')) {
                        $table->dropColumn('due_date');
                    }
                });

        // Source: Modules/Payments/database/migrations/2026_02_03_081912_make_some_fields_null_in_payments_table.php
        Schema::table('payments', function (Blueprint $table) {
                    // اجعل الحقول تقبل القيم الفارغة
                    $table->decimal('amount_usd', 10, 2)->nullable()->change();
                    $table->decimal('amount_syp', 12, 2)->nullable()->change();
                    $table->decimal('exchange_rate_at_payment', 10, 4)->nullable()->change();
                });

        // Source: Modules/Payments/database/migrations/2026_02_05_074639_add_hash_fields_to_payments_table.php
        Schema::table('payments', function (Blueprint $table) {
                    // تغيير الأعمدة إلى نصوص لتخزين القيم المشفرة
                    $table->text('amount_usd')->nullable()->change();
                    $table->text('amount_syp')->nullable()->change();
        
                    // إضافة أعمدة hash جديدة
                    $table->string('amount_usd_hash', 64)->nullable()->after('amount_usd');
                    $table->string('amount_syp_hash', 64)->nullable()->after('amount_syp');
                });
        // Backfill FK after payments table exists.
        if (Schema::hasTable('enrollment_contracts') && Schema::hasColumn('enrollment_contracts', 'first_payment_id')) {
            Schema::table('enrollment_contracts', function (Blueprint $table) {
                $table->foreign('first_payment_id')
                    ->references('id')->on('payments')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};



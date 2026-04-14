<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source: Modules/EnrollmentContracts/database/migrations/2025_09_25_071415_create_enrollment_contracts_table.php
        Schema::create('enrollment_contracts', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('student_id')->constrained();
                    $table->decimal('total_amount_usd', 10, 2)->nullable();
                    $table->decimal('discount_percentage', 5, 2)->nullable();
                    $table->decimal('final_amount_usd', 10, 2)->nullable();
                    $table->decimal('exchange_rate_at_enrollment', 10, 4)->nullable();
                    $table->decimal('final_amount_syp', 12, 2)->nullable();
                    $table->date('agreed_at')->nullable();
                    $table->text('description')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2025_11_02_064713_add_mode_to_enrollment_contracts_table.php
        // migration
                Schema::table('enrollment_contracts', function (Blueprint $table) {
                    $table->enum('mode', ['automatic', 'manual'])->default('automatic');
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2025_11_13_084842_add_paid_amount_usd_to_enrollment_contracts_table.php
        Schema::table('enrollment_contracts', function (Blueprint $table) {
                    $table->decimal('paid_amount_usd', 10, 2)->default(0)->after('final_amount_usd');
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2025_12_09_112553_add_down_payment_and_installments_count_to_enrollment_contracts_table.php
        Schema::table('enrollment_contracts', function (Blueprint $table) {
                    if (!Schema::hasColumn('enrollment_contracts', 'installments_count')) {
                        $table->unsignedInteger('installments_count')->nullable()->after('mode');
                    }
                    if (!Schema::hasColumn('enrollment_contracts', 'down_payment_usd')) {
                        $table->decimal('down_payment_usd', 12, 2)->default(0)->after('installments_count');
                    }
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2025_12_10_084632_add_down_payment_syp_to_enrollment_contracts_table.php
        Schema::table('enrollment_contracts', function (Blueprint $table) {
                    $table->unsignedBigInteger('down_payment_syp')->nullable()->after('down_payment_usd');
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2026_01_03_162711_remove_down_payments_from_enrollment_contracts_table.php
        Schema::table('enrollment_contracts', function (Blueprint $table) {
                    $table->dropColumn([
                        'down_payment_usd',
                        'down_payment_syp',
                    ]);
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2026_02_04_093549_add_first_payment_id_to_enrollment_contracts_table.php
        Schema::table('enrollment_contracts', function (Blueprint $table) {
                    $table->unsignedBigInteger('first_payment_id')
                        ->nullable()
                        ->after('is_active')
                        ->comment('يحوي ID الدفعة الأولى إذا موجودة، أو null إذا لا توجد دفعة أولى');
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2026_02_05_071626_add_encryption_fields_to_enrollment_contracts_table.php
        Schema::table('enrollment_contracts', function (Blueprint $table) {
                    // تعديل الأعمدة الموجودة لتصبح نصوص لتخزين التشفير
                    $table->text('total_amount_usd')->nullable()->change();
                    $table->text('final_amount_usd')->nullable()->change();
                    $table->text('paid_amount_usd')->nullable()->change();
                    $table->text('final_amount_syp')->nullable()->change();
        
                    // إضافة أعمدة hash جديدة
                    $table->string('total_amount_usd_hash', 64)->nullable()->after('total_amount_usd');
                    $table->string('final_amount_usd_hash', 64)->nullable()->after('final_amount_usd');
                    $table->string('paid_amount_usd_hash', 64)->nullable()->after('paid_amount_usd');
                    $table->string('final_amount_syp_hash', 64)->nullable()->after('final_amount_syp');
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2026_02_05_072531_add_discount_percentage_hash_to_enrollment_contracts_table.php
        Schema::table('enrollment_contracts', function (Blueprint $table) {
                    $table->text('discount_percentage')->nullable()->change();
                    $table->string('discount_percentage_hash', 64)->nullable()->after('discount_percentage');
        
                });

        // Source: Modules/EnrollmentContracts/database/migrations/2026_02_15_110230_add_discount_reason_to_enrollment_contracts_table.php
        Schema::table('enrollment_contracts', function (Blueprint $table) {
                    $table->string('discount_reason')->nullable()->after('discount_percentage')
                          ->comment('سبب الخصم، اختياري');
                });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_contracts');
    }
};

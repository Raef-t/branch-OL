<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batch_employees', function (Blueprint $table) {
            $table->id();

            // الربط مع الدفعة (يحذف الربط تلقائياً عند حذف الدفعة)
            $table->foreignId('batch_id')
                  ->constrained('batches')
                  ->cascadeOnDelete();

            // الربط مع الموظف (يحذف الربط تلقائياً عند حذف الموظف)
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->cascadeOnDelete();

            // دور الموظف في الدفعة
            $table->string('role')->default('supervisor');

            // من قام بالتعيين (لا نحذف السجل عند حذف المستخدم)
            $table->foreignId('assigned_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // تاريخ التعيين
            $table->date('assignment_date')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

            // حالة التفعيل
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // منع تكرار نفس الموظف على نفس الدفعة
            $table->unique(['batch_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_employees');
    }
};

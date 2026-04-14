<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('guardian_id')->constrained('guardians')->onDelete('cascade');

            // النوع الجديد فقط
            $table->enum('type', ['phone', 'email', 'address', 'whatsapp']);

            // القيمة أصبحت أطول وقابلة للنل
            $table->string('value', 255)->nullable();

            // الحقول الخاصة بالهاتف فقط
            $table->string('country_code', 5)->nullable();
            $table->string('phone_number', 15)->nullable();

            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_details');
    }
};

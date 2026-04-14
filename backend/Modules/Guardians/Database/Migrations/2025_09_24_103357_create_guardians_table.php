<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_id', 200)->nullable(); // ← الرقم الوطني
            $table->string('phone', 20)->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->string('occupation')->nullable(); 
            $table->text('address')->nullable(); 
            $table->enum('relationship', ['father', 'mother', 'legal_guardian', 'other'])->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('guardians');
    }
};
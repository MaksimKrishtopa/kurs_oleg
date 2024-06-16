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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('surname', 100);
            $table->string('name', 100);
            $table->string('patronymic', 100)->nullable();
            $table->string('gender', 7);
            $table->date('date_of_birth');
            $table->unsignedBigInteger('specialization_id');
            $table->foreign('specialization_id', 'doctor_specialization_fk')
                ->on('specializations')->references('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

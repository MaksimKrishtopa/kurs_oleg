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
        Schema::create('graphs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_and_time', 0);
            $table->unsignedBigInteger('doctor_id');
            $table->foreign('doctor_id', 'graph_doctor_fk')
                ->on('doctors')->references('id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graphs');
    }
};

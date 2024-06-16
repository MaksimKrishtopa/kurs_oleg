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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialization_id');
            $table->foreign('specialization_id', 'record_specialization_fk')
                ->on('specializations')->references('id');
            $table->unsignedBigInteger('doctor_id');
            $table->foreign('doctor_id', 'record_doctor_fk')
                ->on('doctors')->references('id');
            $table->unsignedBigInteger('graph_id');
            $table->foreign('graph_id', 'record_graph_fk')
                ->on('graphs')->references('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'record_user_fk')
                ->on('users')->references('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('surname', 100);
            $table->string('name', 100);
            $table->string('patronymic', 100)->nullable();
            $table->string('gender', 7);
            $table->date('date_of_birth');
            $table->string('phone', 11);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->unsignedBigInteger('role_id')->default(1);
            $table->foreign('role_id', 'user_role_fk')
                ->on('roles')->references('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

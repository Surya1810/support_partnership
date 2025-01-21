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
        Schema::create('user_extensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Relasi ke tabel users
            $table->bigInteger('nik');
            $table->bigInteger('npwp');
            $table->bigInteger('phone');
            $table->string('address');
            $table->string('religion');
            $table->string('gender');
            $table->string('pob');
            $table->date('dob');
            $table->string('hobby');
            $table->string('disease');
            $table->string('marriage');
            $table->string('language');
            $table->string('elementary');
            $table->string('junior_high');
            $table->string('senior_high');
            $table->string('college');
            $table->string('bank');
            $table->bigInteger('account');
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_extensions');
    }
};

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
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('creative_brief');
            $table->string('status');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('assisten_id');
            $table->string('urgency');
            $table->date('deadline');
            $table->date('start');
            // $table->boolean('is_active')->default(true);
            $table->longText('review')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

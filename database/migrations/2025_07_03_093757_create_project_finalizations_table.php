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
        Schema::create('project_finalizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('project_id')
                ->references('id')->on('projects')->onDelete('cascade');
            $table->string('invoice_number');
            $table->string('id_billing');
            $table->string('e_faktur');
            $table->string('ntpn');
            $table->string('bast_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_finalizations');
    }
};

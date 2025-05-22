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
        Schema::create('project_financials', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->decimal('job_value', 15, 2)->default(0);
            $table->enum('vat_percent', [11, 12]); // ppn
            $table->enum('tax_percent', [1.5, 2]); // pph
            $table->decimal('sp2d_amount', 15, 2)->default(0);
            $table->decimal('margin', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_financials');
    }
};

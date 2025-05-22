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
        Schema::create('project_net_profits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_financial_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('company_percent')->nullable();
            $table->decimal('depreciation')->nullable();
            $table->decimal('cash_dept_percent')->nullable(); // kas divisi
            $table->decimal('team_bonus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_net_profits');
    }
};

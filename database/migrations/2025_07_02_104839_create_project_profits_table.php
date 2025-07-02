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
        Schema::create('project_profits', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('project_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->decimal('percent_company')->default(0);
            $table->decimal('percent_depreciation')->default(0);
            $table->decimal('percent_cash_department')->default(0);
            $table->decimal('percent_team_bonus')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_profits');
    }
};

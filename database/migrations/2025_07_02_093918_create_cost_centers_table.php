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
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable();
            $table->foreignId('project_id')->nullable();
            $table->foreignId('cost_center_category_id')->nullable();
            $table->enum('type', ['project', 'department'])->default('department');
            $table->string('code_ref');
            $table->string('name');
            $table->decimal('amount_debit', 15, 2)->default(0);
            $table->decimal('amount_credit', 15, 2)->default(0);
            $table->decimal('amount_remaining', 15, 2)->default(0);
            $table->year('year');
            $table->text('detail')->nullable();
            $table->tinyInteger('month')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
    }
};

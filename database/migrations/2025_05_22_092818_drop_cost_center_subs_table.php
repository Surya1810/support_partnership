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
        Schema::dropIfExists('cost_center_subs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('cost_center_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_center_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->decimal('amount', 15, 2)->nullable();
            $table->timestamps();
        });
    }
};

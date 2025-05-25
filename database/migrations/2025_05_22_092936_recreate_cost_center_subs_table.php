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
        Schema::create('cost_center_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('cost_center_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constained()->onDelete('cascade');
            $table->string('name');

            /**
             * cost_center_category_ref =
             * department code . cost center category code . year . and (transaction number (start: 0001) in expense items)
             */
            $table->string('cost_center_category_ref');
            $table->string('cost_center_category_code');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

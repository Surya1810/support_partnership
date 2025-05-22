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
        Schema::table('expense_items', function (Blueprint $table) {
            /**
             * jika cost center sub id null
             * berarti pengajuan adalah peminjaman atau kebutuhan rumah tangga
             *
             * jika terisi maka ke pengajuan untuk project
             * yang mengarah ke RAB yang diajukan
             */
            $table->foreignId('cost_center_sub_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_items', function (Blueprint $table) {
            //
        });
    }
};

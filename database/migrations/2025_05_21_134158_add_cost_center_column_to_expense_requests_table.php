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
        Schema::table('expense_requests', function (Blueprint $table) {
            /**
             * Jika cost_center_id null di awal pengajuan
             * berarti yang diajukan adalah kebutuhan rumah tangga.
             *
             * Jika project_id null di awal pengajuan
             * dan cost_center_id terisi,
             * berarti pengajuan langsung ke cost_center tertentu
             *
             * Jika pengajuan ke luar cost center milik divisi user
             * berarti pengajuan tersebut masuk sebagai peminjaman
             */
            $table->foreignId('cost_center_id')->nullable()->constrained()->onDelete('set null')->after('department_id');
            $table->string('cost_center_category_name')->nullable();
            $table->string('cost_center_category_code')->nullable();
            $table->string('cost_center_request_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_requests', function (Blueprint $table) {
            //
        });
    }
};

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
            $table->foreignId('cost_center_id')->nullable()->after('department_id');
            $table->string('report_file')->nullable()->after('processed_by_finance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_requests', function (Blueprint $table) {
            $table->dropColumn('cost_center_id');
            $table->dropColumn('report_file');
        });
    }
};

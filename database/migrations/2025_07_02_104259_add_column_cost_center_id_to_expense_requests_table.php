<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('reference_file')->nullable()->after('processed_by_finance');
            $table->string('report_file')->nullable()->after('reference_file');
            $table->text('reason_reject_report')->nullable()->after('report_file');
        });
        DB::statement("ALTER TABLE expense_requests MODIFY status ENUM('pending', 'approved', 'processing', 'report', 'checking', 'finish', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_requests', function (Blueprint $table) {
            $table->dropColumn('cost_center_id');
            $table->dropColumn('reference_file');
            $table->dropColumn('report_file');
            $table->dropColumn('reason_reject_report');
        });
        DB::statement("ALTER TABLE expense_requests MODIFY status ENUM('pending', 'approved', 'processing', 'report', 'finish', 'rejected') DEFAULT 'pending'");
    }
};

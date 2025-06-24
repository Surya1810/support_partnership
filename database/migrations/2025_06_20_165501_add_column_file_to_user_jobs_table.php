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
        Schema::table('user_jobs', function (Blueprint $table) {
            $table->string('report_file')->nullable()->after('notes');
            $table->date('completed_at')->nullable()->after('report_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_jobs', function (Blueprint $table) {
            $table->dropColumn('report_file');
            $table->dropColumn('completed_at');
        });
    }
};

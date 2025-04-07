<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expense_requests', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status'); // Sesuaikan posisi jika perlu
        });
    }

    public function down()
    {
        Schema::table('expense_requests', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};

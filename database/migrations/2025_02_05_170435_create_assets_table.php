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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rfid_number')->unique();
            $table->string('name');
            $table->string('code');
            $table->string('type'); // jenis inventaris : fixed asset , consumable, etc

            $table->string('condition');
            $table->date('tgl_perawatan');

            $table->year('tahun_perolehan');
            $table->decimal('harga_perolehan', 15, 2);
            $table->integer('masa_guna');

            $table->string('status')->nullable(); //dipinjam, dijual, dihibahkan dsb
            $table->text('desc')->nullable(); //kolom tambahan bila diperlukan

            $table->string('gedung');
            $table->string('lantai');
            $table->string('ruangan');

            $table->string('is_there')->default(true);
            $table->string('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

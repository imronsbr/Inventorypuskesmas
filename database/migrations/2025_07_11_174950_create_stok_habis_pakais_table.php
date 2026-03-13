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
        Schema::create('stok_habis_pakais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_barang_habis_pakai_id')->constrained('master_barang_habis_pakais')->onDelete('cascade');
            $table->string('periode');
            $table->integer('stok_awal');
            $table->integer('masuk');
            $table->integer('keluar');
            $table->integer('stok_akhir');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('nilai_akhir', 15, 2)->nullable();
            $table->foreignId('lokasi_id')->nullable()->constrained('ruangs')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_habis_pakais');
    }
};

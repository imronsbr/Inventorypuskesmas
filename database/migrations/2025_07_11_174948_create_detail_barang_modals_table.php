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
        Schema::create('detail_barang_modals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_barang_modal_id')->constrained('master_barang_modals')->onDelete('cascade');
            $table->string('nomor_seri')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->integer('tahun_perolehan')->nullable();
            $table->foreignId('lokasi_id')->nullable()->constrained('ruangs')->nullOnDelete();
            $table->string('kondisi')->nullable();
            $table->string('nie')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barang_modals');
    }
};

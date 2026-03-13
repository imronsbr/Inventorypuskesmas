<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel detail barang
        Schema::create('detail_barangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_barang_id')->nullable();
            $table->string('bahan')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->string('atribut')->nullable();
            $table->decimal('harga', 20, 2)->nullable();
            $table->decimal('kapitalisasi', 20, 2)->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('kode_ruangan');
            $table->string('kode_puskesmas');
            $table->string('status')->nullable(); // baik, rusak ringan, rusak berat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_barangs');
    }
};

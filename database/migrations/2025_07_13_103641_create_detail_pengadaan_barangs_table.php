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
        Schema::create('detail_pengadaan_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_barang_id')->constrained('pengadaan_barangs')->onDelete('cascade');
            
            // Reference to master barang (polymorphic)
            $table->string('barang_type'); // 'App\Models\MasterBarangHabisPakai', 'App\Models\MasterBarangModal', 'App\Models\MasterObat', 'App\Models\MonitoringAlkes'
            $table->unsignedBigInteger('barang_id');
            
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->text('spesifikasi')->nullable();
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengadaan_barangs');
    }
};

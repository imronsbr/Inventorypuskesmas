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
        Schema::create('detail_permintaan_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_barang_id')->constrained()->onDelete('cascade');
            $table->foreignId('master_barang_habis_pakai_id')->nullable()->constrained('master_barang_habis_pakais')->onDelete('cascade');
            $table->foreignId('master_barang_modal_id')->nullable()->constrained('master_barang_modals')->onDelete('cascade');
            $table->foreignId('master_obat_id')->nullable()->constrained('master_obats')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('jumlah_diberikan')->default(0);
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_approval')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_permintaan_barangs');
    }
};

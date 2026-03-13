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
        Schema::create('permintaan_barang_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_barang_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'diproses']);
            $table->text('catatan')->nullable();
            $table->foreignId('master_barang_habis_pakai_id')->nullable()->constrained('master_barang_habis_pakais')->onDelete('cascade');
            $table->foreignId('master_barang_modal_id')->nullable()->constrained('master_barang_modals')->onDelete('cascade');
            $table->foreignId('master_obat_id')->nullable()->constrained('master_obats')->onDelete('cascade');
            $table->integer('jumlah_diajukan')->default(0);
            $table->integer('jumlah_diberikan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_barang_logs');
    }
};

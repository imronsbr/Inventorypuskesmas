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
        Schema::create('permintaan_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('puskesmas_id')->constrained()->onDelete('cascade');
            $table->foreignId('ruang_id')->constrained()->onDelete('cascade');
            $table->datetime('tanggal_pesanan');
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'diproses'])->default('diajukan');
            $table->text('catatan')->nullable();
            $table->foreignId('penanggung_jawab_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('penanggung_jawab_jabatan_id')->nullable()->constrained('jabatans')->onDelete('set null');
            $table->foreignId('pptk_jabatan_id')->nullable()->constrained('jabatans')->onDelete('set null');
            $table->foreignId('perencana_jabatan_id')->nullable()->constrained('jabatans')->onDelete('set null');
            $table->foreignId('kepala_puskesmas_jabatan_id')->nullable()->constrained('jabatans')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_barangs');
    }
};

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
        Schema::create('pengadaan_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengadaan')->unique();
            $table->string('jenis_barang'); // 'habis_pakai', 'modal', 'obat', 'alkes'
            $table->text('keterangan')->nullable();
            $table->decimal('total_anggaran', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, submitted, approved_penjab, approved_pptk, approved_perencana, approved_kepala_puskesmas, approved_kepala_tata_usaha, approved, rejected, completed
            $table->text('alasan_revisi')->nullable();
            $table->text('alasan_reject')->nullable();
            
            // User yang membuat
            $table->foreignId('created_by')->constrained('users');
            
            // Approval chain
            $table->foreignId('approved_by_penjab')->nullable()->constrained('users');
            $table->timestamp('approved_at_penjab')->nullable();
            $table->foreignId('approved_by_pptk')->nullable()->constrained('users');
            $table->timestamp('approved_at_pptk')->nullable();
            $table->foreignId('approved_by_perencana')->nullable()->constrained('users');
            $table->timestamp('approved_at_perencana')->nullable();
            $table->foreignId('approved_by_kepala_puskesmas')->nullable()->constrained('users');
            $table->timestamp('approved_at_kepala_puskesmas')->nullable();
            $table->foreignId('approved_by_kepala_tata_usaha')->nullable()->constrained('users');
            $table->timestamp('approved_at_kepala_tata_usaha')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_barangs');
    }
};

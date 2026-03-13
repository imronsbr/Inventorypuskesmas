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
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            // Add missing columns
            $table->decimal('harga', 20, 2)->nullable()->after('keterangan');
            $table->decimal('kapitalisasi', 20, 2)->nullable()->after('harga');
            $table->integer('jumlah')->default(1)->after('kapitalisasi');
            $table->string('kode_ruangan')->nullable()->after('jumlah');
            $table->string('kode_puskesmas')->nullable()->after('kode_ruangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            $table->dropColumn(['harga', 'kapitalisasi', 'jumlah', 'kode_ruangan', 'kode_puskesmas']);
        });
    }
};

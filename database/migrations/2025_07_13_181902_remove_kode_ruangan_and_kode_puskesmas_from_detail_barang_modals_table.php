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
            // Remove kode_ruangan and kode_puskesmas columns
            $table->dropColumn(['kode_ruangan', 'kode_puskesmas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            // Add back the columns if migration is rolled back
            $table->string('kode_ruangan')->nullable()->after('jumlah');
            $table->string('kode_puskesmas')->nullable()->after('kode_ruangan');
        });
    }
};

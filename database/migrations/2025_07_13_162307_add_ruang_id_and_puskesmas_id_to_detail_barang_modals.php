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
            $table->unsignedBigInteger('ruang_id')->nullable();
            $table->unsignedBigInteger('puskesmas_id')->nullable();

            $table->foreign('ruang_id')->references('id')->on('ruangs')->nullOnDelete();
            $table->foreign('puskesmas_id')->references('id')->on('puskesmas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            $table->dropForeign(['ruang_id']);
            $table->dropForeign(['puskesmas_id']);
            $table->dropColumn(['ruang_id', 'puskesmas_id']);
        });
    }
};

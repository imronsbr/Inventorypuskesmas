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
            $table->dropForeign(['puskesmas_id']);
            $table->dropColumn('puskesmas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            $table->unsignedBigInteger('puskesmas_id')->nullable();
            $table->foreign('puskesmas_id')->references('id')->on('puskesmas')->nullOnDelete();
        });
    }
};

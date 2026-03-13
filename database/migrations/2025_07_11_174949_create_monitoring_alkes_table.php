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
        Schema::create('monitoring_alkes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_barang_modal_id')->constrained('detail_barang_modals')->onDelete('cascade');
            $table->date('tanggal_monitor');
            $table->string('kondisi');
            $table->string('posisi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_alkes');
    }
};

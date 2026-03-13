<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create STR table
        Schema::create('str', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->onDelete('set null');
            $table->string('nomor_str')->unique();
            $table->date('tanggal_terbit');
            $table->date('tanggal_berakhir');
            $table->timestamps();
        });

        // Create SIP table
        Schema::create('sip', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->onDelete('set null');
            $table->string('nomor_sip')->unique();
            $table->string('tempat_praktik');
            $table->date('tanggal_terbit');
            $table->date('tanggal_berakhir');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sip');
        Schema::dropIfExists('str');
    }
};

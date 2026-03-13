<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('unit_kerja')->nullable();
            $table->foreignId('agama_id')->nullable()->constrained('agamas')->nullOnDelete();
            $table->foreignId('pendidikan_id')->nullable()->constrained('pendidikans')->nullOnDelete();
            $table->enum('status_pegawai', ['pns', 'cpns', 'non_pns', 'pjlp']);
            $table->softDeletes();
            $table->enum('jenis_kontrak', ['tetap', 'kontrak'])->nullable();
            $table->string('nik')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('golongan')->nullable();
            $table->string('npwp')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tmt')->nullable();
            $table->date('tmt_pensiun')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};

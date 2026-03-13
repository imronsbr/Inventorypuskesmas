<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBarang extends Model
{
    protected $fillable = [
        'master_barang_id', 'bahan', 'merk', 'tipe', 'atribut', 'harga', 'kapitalisasi', 'jumlah', 'kode_ruangan', 'kode_puskesmas', 'status'
    ];

    // Status constants
    const STATUS_BAIK = 'baik';
    const STATUS_RUSAK_RINGAN = 'rusak_ringan';
    const STATUS_RUSAK_BERAT = 'rusak_berat';

    // Methods
    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_BAIK => 'Baik',
            self::STATUS_RUSAK_RINGAN => 'Rusak Ringan',
            self::STATUS_RUSAK_BERAT => 'Rusak Berat',
            default => 'Tidak Diketahui',
        };
    }

    public function getStatusBadge()
    {
        return match($this->status) {
            self::STATUS_BAIK => 'badge-success',
            self::STATUS_RUSAK_RINGAN => 'badge-warning',
            self::STATUS_RUSAK_BERAT => 'badge-error',
            default => 'badge-secondary',
        };
    }

    public function masterBarang()
    {
        return $this->belongsTo(MasterBarangHabisPakai::class, 'master_barang_id');
    }

    public function logPerubahan()
    {
        return $this->hasMany(LogPerubahan::class, 'detail_barang_id');
    }
}

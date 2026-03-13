<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanBarangLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permintaan_barang_logs';

    protected $fillable = [
        'permintaan_barang_id',
        'user_id',
        'status',
        'catatan',
        'master_barang_habis_pakai_id',
        'master_barang_modal_id',
        'master_obat_id',
        'jumlah_diajukan',
        'jumlah_diberikan',
    ];

    // Relationships
    public function permintaanBarang()
    {
        return $this->belongsTo(PermintaanBarang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function masterBarangHabisPakai()
    {
        return $this->belongsTo(MasterBarangHabisPakai::class);
    }

    public function masterBarangModal()
    {
        return $this->belongsTo(MasterBarangModal::class);
    }

    public function masterObat()
    {
        return $this->belongsTo(MasterObat::class);
    }

    // Helper method to get the master barang regardless of type
    public function getMasterBarangAttribute()
    {
        if ($this->master_barang_habis_pakai_id) {
            return $this->masterBarangHabisPakai;
        }
        if ($this->master_barang_modal_id) {
            return $this->masterBarangModal;
        }
        if ($this->master_obat_id) {
            return $this->masterObat;
        }
        return null;
    }
} 
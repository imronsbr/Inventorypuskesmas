<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPermintaanBarang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_permintaan_barangs';

    protected $fillable = [
        'permintaan_barang_id',
        'master_barang_habis_pakai_id',
        'master_barang_modal_id',
        'master_obat_id',
        'jumlah',
        'jumlah_diberikan',
        'status',
        'catatan_approval',
    ];

    // Relationships
    public function permintaanBarang()
    {
        return $this->belongsTo(PermintaanBarang::class);
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPengadaanBarang extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'pengadaan_barang_id',
        'barang_type',
        'barang_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'spesifikasi',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    // Relationships
    public function pengadaanBarang()
    {
        return $this->belongsTo(PengadaanBarang::class);
    }

    public function barang()
    {
        return $this->morphTo();
    }

    // Polymorphic relationships for different barang types
    public function masterBarangHabisPakai()
    {
        return $this->belongsTo(MasterBarangHabisPakai::class, 'barang_id')
            ->where('barang_type', MasterBarangHabisPakai::class);
    }

    public function masterBarangModal()
    {
        return $this->belongsTo(MasterBarangModal::class, 'barang_id')
            ->where('barang_type', MasterBarangModal::class);
    }

    public function masterObat()
    {
        return $this->belongsTo(MasterObat::class, 'barang_id')
            ->where('barang_type', MasterObat::class);
    }

    public function monitoringAlkes()
    {
        return $this->belongsTo(MonitoringAlkes::class, 'barang_id')
            ->where('barang_type', MonitoringAlkes::class);
    }

    // Methods
    public function getBarangName()
    {
        if ($this->barang) {
            return $this->barang->nama_barang ?? $this->barang->nama ?? 'Unknown';
        }
        return 'Unknown';
    }

    public function getBarangKode()
    {
        if ($this->barang) {
            return $this->barang->kode_barang ?? 'Unknown';
        }
        return 'Unknown';
    }

    public function getBarangSatuan()
    {
        if ($this->barang) {
            return $this->barang->satuan ?? 'Unknown';
        }
        return 'Unknown';
    }

    // Boot method to auto-calculate total_harga
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->total_harga = $model->jumlah * $model->harga_satuan;
        });

        static::updating(function ($model) {
            $model->total_harga = $model->jumlah * $model->harga_satuan;
        });
    }
}

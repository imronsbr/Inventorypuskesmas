<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokHabisPakai extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'master_barang_habis_pakai_id', 'periode', 'stok_awal', 'masuk', 'keluar', 'stok_akhir', 'harga_satuan', 'nilai_akhir', 'lokasi_id', 'keterangan'
    ];

    protected $casts = [
        'stok_awal' => 'integer',
        'masuk' => 'integer',
        'keluar' => 'integer',
        'stok_akhir' => 'integer',
        'harga_satuan' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    // Boot method to auto-calculate stok_akhir and nilai_akhir
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->stok_akhir = $model->stok_awal + $model->masuk - $model->keluar;
            $model->nilai_akhir = $model->stok_akhir * $model->harga_satuan;
        });

        static::updating(function ($model) {
            $model->stok_akhir = $model->stok_awal + $model->masuk - $model->keluar;
            $model->nilai_akhir = $model->stok_akhir * $model->harga_satuan;
        });
    }

    // Relationship to master barang
    public function masterBarangHabisPakai()
    {
        return $this->belongsTo(MasterBarangHabisPakai::class, 'master_barang_habis_pakai_id');
    }

    // Relationship to location/ruang
    public function lokasi()
    {
        return $this->belongsTo(Ruang::class, 'lokasi_id');
    }

    // Get stock movement type
    public function getMovementTypeAttribute()
    {
        if ($this->masuk > 0 && $this->keluar == 0) {
            return 'masuk';
        } elseif ($this->keluar > 0 && $this->masuk == 0) {
            return 'keluar';
        } else {
            return 'koreksi';
        }
    }

    // Get stock movement description
    public function getMovementDescriptionAttribute()
    {
        $type = $this->movement_type;
        $amount = $type === 'masuk' ? $this->masuk : $this->keluar;
        
        return ucfirst($type) . ' ' . $amount . ' ' . ($this->masterBarangHabisPakai->satuan ?? 'unit');
    }

    // Scope for specific period
    public function scopePeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    // Scope for specific location
    public function scopeLokasi($query, $lokasiId)
    {
        return $query->where('lokasi_id', $lokasiId);
    }

    // Scope for stock movements (masuk/keluar)
    public function scopeMovement($query, $type = null)
    {
        if ($type === 'masuk') {
            return $query->where('masuk', '>', 0);
        } elseif ($type === 'keluar') {
            return $query->where('keluar', '>', 0);
        }
        
        return $query;
    }
}

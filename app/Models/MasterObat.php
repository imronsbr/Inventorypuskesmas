<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterObat extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'kode_obat', 'nama_obat', 'satuan', 'harga_satuan', 'bentuk', 'komposisi', 'kategori', 'keterangan', 'stok'
    ];

    // Accessor to provide 'nama' attribute for consistency
    public function getNamaAttribute()
    {
        return $this->nama_obat;
    }

    // Accessor untuk status kondisi berdasarkan stok
    public function getStatusKondisiAttribute()
    {
        if ($this->stok === null || $this->stok === 0) {
            return 'habis';
        } elseif ($this->stok <= 10) {
            return 'stok_menipis';
        } else {
            return 'tersedia';
        }
    }

    // Accessor untuk label status kondisi
    public function getStatusKondisiLabelAttribute()
    {
        $labels = [
            'tersedia' => 'Tersedia',
            'stok_menipis' => 'Stok Menipis',
            'habis' => 'Habis'
        ];
        
        return $labels[$this->status_kondisi] ?? 'Tidak Diketahui';
    }

    // Accessor untuk badge class status kondisi
    public function getStatusKondisiBadgeAttribute()
    {
        $badges = [
            'tersedia' => 'badge-success',
            'stok_menipis' => 'badge-warning',
            'habis' => 'badge-error'
        ];
        
        return $badges[$this->status_kondisi] ?? 'badge-secondary';
    }
}

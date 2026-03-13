<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBarangModal extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'kode_barang', 'nama_barang', 'satuan', 'kategori', 'keterangan'
    ];

    // Accessor to provide 'nama' attribute for consistency
    public function getNamaAttribute()
    {
        return $this->nama_barang;
    }

    public function detailBarangModals()
    {
        return $this->hasMany(DetailBarangModal::class, 'kode_barang', 'kode_barang');
    }

    // Status methods based on detail barang conditions
    public function getStatusKondisi()
    {
        $details = $this->detailBarangModals;
        
        if ($details->isEmpty()) {
            return 'tidak_ada_data';
        }

        $totalItems = $details->count();
        $baikCount = $details->where('kondisi', DetailBarangModal::KONDISI_BAIK)->count();
        $perbaikanCount = $details->where('kondisi', DetailBarangModal::KONDISI_PERBAIKAN)->count();
        $rusakBeratCount = $details->where('kondisi', DetailBarangModal::KONDISI_RUSAK_BERAT)->count();

        // Jika semua item dalam kondisi baik
        if ($baikCount === $totalItems) {
            return 'baik';
        }

        // Jika ada item rusak berat, status rusak berat
        if ($rusakBeratCount > 0) {
            return 'rusak_berat';
        }

        // Jika ada item perbaikan, status perbaikan
        if ($perbaikanCount > 0) {
            return 'perbaikan';
        }

        // Default ke baik jika tidak ada kondisi lain
        return 'baik';
    }

    public function getStatusKondisiLabel()
    {
        return match($this->getStatusKondisi()) {
            'baik' => 'Baik',
            'perbaikan' => 'Perbaikan',
            'rusak_berat' => 'Rusak Berat',
            'tidak_ada_data' => 'Tidak Ada Data',
            default => 'Tidak Diketahui',
        };
    }

    public function getStatusKondisiBadge()
    {
        return match($this->getStatusKondisi()) {
            'baik' => 'badge-success',
            'perbaikan' => 'badge-warning',
            'rusak_berat' => 'badge-error',
            'tidak_ada_data' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }

    public function getStatusKondisiSummary()
    {
        $details = $this->detailBarangModals;
        
        if ($details->isEmpty()) {
            return 'Tidak ada data detail barang';
        }

        $totalItems = $details->count();
        $baikCount = $details->where('kondisi', DetailBarangModal::KONDISI_BAIK)->count();
        $perbaikanCount = $details->where('kondisi', DetailBarangModal::KONDISI_PERBAIKAN)->count();
        $rusakBeratCount = $details->where('kondisi', DetailBarangModal::KONDISI_RUSAK_BERAT)->count();

        $summary = [];
        if ($baikCount > 0) {
            $summary[] = "Baik: {$baikCount}";
        }
        if ($perbaikanCount > 0) {
            $summary[] = "Perbaikan: {$perbaikanCount}";
        }
        if ($rusakBeratCount > 0) {
            $summary[] = "Rusak Berat: {$rusakBeratCount}";
        }

        return implode(', ', $summary) . " (Total: {$totalItems})";
    }
}

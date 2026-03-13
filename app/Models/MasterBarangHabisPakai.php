<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBarangHabisPakai extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'kode_barang', 'nama_barang', 'spesifikasi', 'satuan', 'kategori', 'harga_satuan', 'keterangan'
    ];

    // Accessor to provide 'nama' attribute for consistency
    public function getNamaAttribute()
    {
        return $this->nama_barang ?? '';
    }

    // Accessor to get current stock from latest transaction
    public function getStokAttribute()
    {
        $latestStock = $this->stokHabisPakais()->latest()->first();
        return $latestStock ? (int)$latestStock->stok_akhir : 0;
    }

    // Get current stock for specific location
    public function getStokLokasi($lokasiId = null)
    {
        $query = $this->stokHabisPakais()->latest();
        
        if ($lokasiId) {
            $query->where('lokasi_id', $lokasiId);
        }
        
        $latestStock = $query->first();
        return $latestStock ? $latestStock->stok_akhir : 0;
    }

    // Get stock history for specific period
    public function getStokHistory($periode = null)
    {
        $query = $this->stokHabisPakais();
        
        if ($periode) {
            $query->where('periode', $periode);
        }
        
        return $query->orderBy('created_at')->get();
    }

    // Relationship to stok transactions
    public function stokHabisPakais()
    {
        return $this->hasMany(StokHabisPakai::class, 'master_barang_habis_pakai_id');
    }

    // Get latest stock transaction
    public function latestStok()
    {
        return $this->hasOne(StokHabisPakai::class, 'master_barang_habis_pakai_id')->latest();
    }

    // Relationship to pengadaan barang (polymorphic)
    public function pengadaanBarang()
    {
        return $this->morphMany(DetailPengadaanBarang::class, 'barang')
            ->where('barang_type', self::class);
    }

    // Relationship to permintaan barang via detail
    public function permintaanBarang()
    {
        // Only get permintaan barang yang benar-benar terkait barang ini
        return $this->hasManyThrough(
            PermintaanBarang::class,
            DetailPermintaanBarang::class,
            'master_barang_habis_pakai_id', // Foreign key on detail_permintaan_barangs
            'id', // Foreign key on permintaan_barangs
            'id', // Local key on master_barang_habis_pakais
            'permintaan_barang_id' // Local key on detail_permintaan_barangs
        );
    }

    // Scope: hanya bisa diminta jika barang aktif dan stok > 0
    public function scopeBisaDiminta($query)
    {
        return $query->whereNull('deleted_at')
            ->whereHas('stokHabisPakais', function($q) {
                $q->where('stok_akhir', '>', 0);
            });
    }

    // Validation methods for delete
    public function canBeDeleted()
    {
        $currentStock = $this->stokHabisPakais()->where('stok_akhir', '>', 0)->count();
        
        // Check pending pengadaan (if PengadaanBarang model exists)
        $pendingPengadaan = 0;
        if (class_exists(PengadaanBarang::class)) {
            $pendingPengadaan = $this->pengadaanBarang()
                ->whereHas('pengadaanBarang', function($q) {
                    $q->whereIn('status', [
                        'draft', 'submitted', 'approved_penjab', 'approved_pptk', 
                        'approved_perencana', 'approved_kepala_puskesmas'
                    ]);
                })->count();
        }
        
        // Check pending permintaan (if PermintaanBarang model exists)
        $pendingPermintaan = 0;
        if (class_exists(PermintaanBarang::class)) {
            $pendingPermintaan = $this->permintaanBarang()
                ->whereIn('status', ['draft', 'submitted', 'approved_user', 'approved_penjab'])
                ->count();
        }

        return [
            'can_delete' => $currentStock === 0 && $pendingPengadaan === 0 && $pendingPermintaan === 0,
            'current_stock' => $currentStock,
            'pending_pengadaan' => $pendingPengadaan,
            'pending_permintaan' => $pendingPermintaan,
        ];
    }

    public function getDeleteValidationMessage()
    {
        $validation = $this->canBeDeleted();
        $reasons = [];

        if ($validation['current_stock'] > 0) {
            $reasons[] = "Masih memiliki {$validation['current_stock']} stok aktif";
        }

        if ($validation['pending_pengadaan'] > 0) {
            $reasons[] = "Masih ada {$validation['pending_pengadaan']} pengadaan dalam proses approval";
        }

        if ($validation['pending_permintaan'] > 0) {
            $reasons[] = "Masih ada {$validation['pending_permintaan']} permintaan dalam proses approval";
        }

        return $reasons;
    }

    // Get status for display
    public function getStatusAttribute()
    {
        if ($this->trashed()) return 'deleted';
        
        $validation = $this->canBeDeleted();
        
        if ($validation['current_stock'] > 0) return 'active';
        if ($validation['pending_pengadaan'] > 0) return 'pending_pengadaan';
        if ($validation['pending_permintaan'] > 0) return 'pending_permintaan';
        return 'inactive';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'badge-success',
            'pending_pengadaan' => 'badge-warning',
            'pending_permintaan' => 'badge-info',
            'inactive' => 'badge-secondary',
            'deleted' => 'badge-error',
            default => 'badge-secondary'
        };
    }
}

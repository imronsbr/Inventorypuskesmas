<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengadaanBarang extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nomor_pengadaan',
        'jenis_barang',
        'keterangan',
        'total_anggaran',
        'status',
        'alasan_revisi',
        'alasan_reject',
        'created_by',
        'approved_by_penjab',
        'approved_at_penjab',
        'approved_by_pptk',
        'approved_at_pptk',
        'approved_by_perencana',
        'approved_at_perencana',
        'approved_by_kepala_puskesmas',
        'approved_at_kepala_puskesmas',
        'approved_by_kepala_tata_usaha',
        'approved_at_kepala_tata_usaha',
    ];

    protected $casts = [
        'total_anggaran' => 'decimal:2',
        'approved_at_penjab' => 'datetime',
        'approved_at_pptk' => 'datetime',
        'approved_at_perencana' => 'datetime',
        'approved_at_kepala_puskesmas' => 'datetime',
        'approved_at_kepala_tata_usaha' => 'datetime',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED_PENJAB = 'approved_penjab';
    const STATUS_APPROVED_PPTK = 'approved_pptk';
    const STATUS_APPROVED_PERENCANA = 'approved_perencana';
    const STATUS_APPROVED_KEPALA_PUSKESMAS = 'approved_kepala_puskesmas';
    const STATUS_APPROVED_KEPALA_TATA_USAHA = 'approved_kepala_tata_usaha';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    // Jenis barang constants
    const JENIS_HABIS_PAKAI = 'habis_pakai';
    const JENIS_MODAL = 'modal';
    const JENIS_OBAT = 'obat';
    const JENIS_ALKES = 'alkes';

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedByPenjab()
    {
        return $this->belongsTo(User::class, 'approved_by_penjab');
    }

    public function approvedByPptk()
    {
        return $this->belongsTo(User::class, 'approved_by_pptk');
    }

    public function approvedByPerencana()
    {
        return $this->belongsTo(User::class, 'approved_by_perencana');
    }

    public function approvedByKepalaPuskesmas()
    {
        return $this->belongsTo(User::class, 'approved_by_kepala_puskesmas');
    }

    public function approvedByKepalaTataUsaha()
    {
        return $this->belongsTo(User::class, 'approved_by_kepala_tata_usaha');
    }

    public function detailPengadaanBarangs()
    {
        return $this->hasMany(DetailPengadaanBarang::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJenisBarang($query, $jenis)
    {
        return $query->where('jenis_barang', $jenis);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_APPROVED_PENJAB,
            self::STATUS_APPROVED_PPTK,
            self::STATUS_APPROVED_PERENCANA,
            self::STATUS_APPROVED_KEPALA_PUSKESMAS,
        ]);
    }

    // Methods
    public function canBeApprovedByRole($role)
    {
        return match($role) {
            'penjab' => in_array($this->status, [self::STATUS_SUBMITTED]),
            'pptk' => in_array($this->status, [self::STATUS_APPROVED_PENJAB]),
            'perencana' => in_array($this->status, [self::STATUS_APPROVED_PPTK]),
            'kepala_puskesmas' => in_array($this->status, [self::STATUS_APPROVED_PERENCANA]),
            'kepala_tata_usaha' => in_array($this->status, [self::STATUS_APPROVED_KEPALA_PUSKESMAS]),
            default => false,
        };
    }

    public function getNextApprover()
    {
        return match($this->status) {
            self::STATUS_DRAFT => null,
            self::STATUS_SUBMITTED => 'penjab',
            self::STATUS_APPROVED_PENJAB => 'pptk',
            self::STATUS_APPROVED_PPTK => 'perencana',
            self::STATUS_APPROVED_PERENCANA => 'kepala_puskesmas',
            self::STATUS_APPROVED_KEPALA_PUSKESMAS => 'kepala_tata_usaha',
            self::STATUS_APPROVED_KEPALA_TATA_USAHA => null,
            default => null,
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_APPROVED_PENJAB => 'Approved Penjab',
            self::STATUS_APPROVED_PPTK => 'Approved PPTK',
            self::STATUS_APPROVED_PERENCANA => 'Approved Perencana',
            self::STATUS_APPROVED_KEPALA_PUSKESMAS => 'Approved Kepala Puskesmas',
            self::STATUS_APPROVED_KEPALA_TATA_USAHA => 'Approved Kepala Tata Usaha',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_COMPLETED => 'Completed',
            default => 'Unknown',
        };
    }

    public function getStatusBadge()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'badge-secondary',
            self::STATUS_SUBMITTED => 'badge-info',
            self::STATUS_APPROVED_PENJAB => 'badge-warning',
            self::STATUS_APPROVED_PPTK => 'badge-warning',
            self::STATUS_APPROVED_PERENCANA => 'badge-warning',
            self::STATUS_APPROVED_KEPALA_PUSKESMAS => 'badge-warning',
            self::STATUS_APPROVED_KEPALA_TATA_USAHA => 'badge-success',
            self::STATUS_APPROVED => 'badge-success',
            self::STATUS_REJECTED => 'badge-error',
            self::STATUS_COMPLETED => 'badge-success',
            default => 'badge-secondary',
        };
    }

    // Dynamic approval methods
    public function getCurrentApprover()
    {
        return match($this->status) {
            self::STATUS_SUBMITTED => $this->getUserByJabatan('Penanggung Jawab Barang'),
            self::STATUS_APPROVED_PENJAB => $this->getUserByJabatan('PPTK'),
            self::STATUS_APPROVED_PPTK => $this->getUserByJabatan('Perencana'),
            self::STATUS_APPROVED_PERENCANA => $this->getUserByJabatan('Kepala Puskesmas'),
            self::STATUS_APPROVED_KEPALA_PUSKESMAS => $this->getUserByJabatan('Kepala Tata Usaha'),
            default => null,
        };
    }

    public function getUserByJabatan($namaJabatan)
    {
        return User::whereHas('pegawai.jabatans', function($q) use ($namaJabatan) {
            $q->where('nama', $namaJabatan)
              ->where(function($subQ) {
                  $subQ->whereNull('tanggal_selesai')
                        ->orWhere('tanggal_selesai', '>=', now());
              });
        })->first();
    }

    public function canBeApprovedBy($user)
    {
        $currentApprover = $this->getCurrentApprover();
        return $currentApprover && $currentApprover->id === $user->id;
    }
}

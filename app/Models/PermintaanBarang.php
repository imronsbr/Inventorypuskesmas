<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanBarang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permintaan_barangs';

    protected $fillable = [
        'user_id',
        'puskesmas_id',
        'ruang_id',
        'tanggal_pesanan',
        'status',
        'catatan',
        'penanggung_jawab_id',
        'penanggung_jawab_jabatan_id',
        'pptk_jabatan_id',
        'perencana_jabatan_id',
        'kepala_puskesmas_jabatan_id',
    ];

    protected $casts = [
        'tanggal_pesanan' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function ruang()
    {
        return $this->belongsTo(Ruang::class);
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }

    public function details()
    {
        return $this->hasMany(DetailPermintaanBarang::class);
    }

    public function logs()
    {
        return $this->hasMany(PermintaanBarangLog::class);
    }

    // Accessors
    public function getJumlahItemAttribute()
    {
        return $this->details->sum('jumlah');
    }

    public function getWaktuProsesAttribute()
    {
        if ($this->status === 'diajukan') {
            return '-';
        }
        
        $approvedLog = $this->logs()->where('status', 'disetujui')->first();
        if ($approvedLog) {
            $days = $this->tanggal_pesanan->diffInDays($approvedLog->created_at);
            return $days . ' hari';
        }
        
        return '-';
    }

    // Dynamic approval methods
    public function getCurrentApprover()
    {
        return match($this->status) {
            'diajukan' => $this->getUserByJabatan('Penanggung Jawab Barang'),
            'disetujui_penjab' => $this->getUserByJabatan('PPTK'),
            'disetujui_pptk' => $this->getUserByJabatan('Kepala Puskesmas'),
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

    public function getNextStatus()
    {
        return match($this->status) {
            'diajukan' => 'disetujui_penjab',
            'disetujui_penjab' => 'disetujui_pptk',
            'disetujui_pptk' => 'disetujui_kepala_puskesmas',
            'disetujui_kepala_puskesmas' => 'disetujui',
            default => null,
        };
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sip extends Model
{
    use HasFactory;
    protected $table = 'sip';
    protected $fillable = [
        'pegawai_id',
        'nomor_sip',
        'tempat_praktik',
        'tanggal_terbit',
        'tanggal_berakhir',
        'file_path',
    ];
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
} 
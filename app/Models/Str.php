<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Str extends Model
{
    use HasFactory;
    protected $table = 'str';
    protected $fillable = [
        'pegawai_id',
        'nomor_str',
        'tanggal_terbit',
        'tanggal_berakhir',
        'file_path',
    ];
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
} 
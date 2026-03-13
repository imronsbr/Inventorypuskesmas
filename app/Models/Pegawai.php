<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Agama;
use App\Models\Pendidikan;
use App\Models\Keluarga;

class Pegawai extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'pegawais';
    protected $fillable = [
        'user_id', 'nama', 'nip', 'unit_kerja', 'agama_id', 'pendidikan_id', 'status_pegawai', 'jenis_kontrak',
        'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'golongan', 'npwp', 'no_hp', 'alamat', 'tmt', 'tmt_pensiun', 'keterangan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function jabatans()
    {
        return $this->belongsToMany(Jabatan::class, 'jabatan_pegawai')
            ->withPivot('tanggal_mulai', 'tanggal_selesai')
            ->withTimestamps();
    }
    public function strs()
    {
        return $this->hasMany(\App\Models\Str::class, 'pegawai_id');
    }

    public function sips()
    {
        return $this->hasMany(\App\Models\Sip::class, 'pegawai_id');
    }
    public function agama()
    {
        return $this->belongsTo(Agama::class);
    }
    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class);
    }
    public function keluargas()
    {
        return $this->hasMany(Keluarga::class);
    }
}

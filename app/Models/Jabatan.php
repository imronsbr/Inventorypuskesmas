<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jabatan extends Model
{
    use HasFactory;
    protected $table = 'jabatans';
    protected $fillable = ['nama', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Jabatan::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Jabatan::class, 'parent_id');
    }
    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'jabatan_pegawai');
    }
} 
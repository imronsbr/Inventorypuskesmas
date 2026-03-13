<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruang extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'ruangs';
    protected $fillable = ['kode', 'nama', 'puskesmas_id'];

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function detailBarangModals()
    {
        return $this->hasMany(DetailBarangModal::class, 'ruang_id');
    }
}

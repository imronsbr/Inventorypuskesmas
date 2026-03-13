<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pegawai;

class Keluarga extends Model
{
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}

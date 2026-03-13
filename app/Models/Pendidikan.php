<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pegawai;

class Pendidikan extends Model
{
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}

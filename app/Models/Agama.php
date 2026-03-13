<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pegawai;

class Agama extends Model
{
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}

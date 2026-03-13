<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Puskesmas extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'puskesmas';
    protected $fillable = ['kode', 'nama'];

    public function ruangs()
    {
        return $this->hasMany(Ruang::class);
    }
}

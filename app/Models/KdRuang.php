<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KdRuang extends Model
{
    use HasFactory;
    protected $table = 'kd_ruangs';
    protected $fillable = [
        'kode',
        'nama',
    ];
}

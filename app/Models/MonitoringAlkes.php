<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonitoringAlkes extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'detail_barang_modal_id', 'tanggal_monitor', 'kondisi', 'posisi', 'keterangan'
    ];

    public function detailBarangModal()
    {
        return $this->belongsTo(DetailBarangModal::class, 'detail_barang_modal_id');
    }
}

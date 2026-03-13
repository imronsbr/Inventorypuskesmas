<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Barang([
            'kobar' => $row['kobar'] ?? null,
            'reg' => $row['reg'] ?? null,
            'jenis_barang' => $row['jenis_barang'] ?? null,
            'ukuran' => $row['ukuran'] ?? null,
            'satuan' => $row['satuan'] ?? null,
            'tgl_oleh' => $row['tgl_oleh'] ?? null,
            'kotakabupaten' => $row['kotakabupaten'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kelurahan' => $row['kelurahan'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'no_jalan' => $row['no_jalan'] ?? null,
            'rt' => $row['rt'] ?? null,
            'rw' => $row['rw'] ?? null,
            'bahan' => $row['bahan'] ?? null,
            'merk' => $row['merk'] ?? null,
            'tipe' => $row['tipe'] ?? null,
            'tgl_bpkb' => $row['tgl_bpkb'] ?? null,
            'no_chasis' => $row['no_chasis'] ?? null,
            'no_mesin' => $row['no_mesin'] ?? null,
            'nomor_polisi' => $row['nomor_polisi'] ?? null,
            'asal_oleh' => $row['asal_oleh'] ?? null,
            'harga' => $row['harga'] ?? null,
            'kapitalisasi' => $row['kapitalisasi'] ?? null,
            'total' => $row['total'] ?? null,
            'kd_ruang' => $row['kd_ruang'] ?? null,
        ]);
    }
}

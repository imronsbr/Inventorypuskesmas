<?php

namespace App\Exports;

use App\Models\MasterBarangHabisPakai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterBarangHabisPakaiExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return MasterBarangHabisPakai::all();
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Kategori',
            'Keterangan',
            'Status'
        ];
    }

    public function map($barang): array
    {
        return [
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->satuan,
            $barang->kategori,
            $barang->keterangan ?? '',
            $barang->stok > 0 ? 'Aktif' : 'Habis'
        ];
    }
} 
<?php

namespace App\Exports;

use App\Models\MasterBarangModal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterBarangModalExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return MasterBarangModal::select([
            'id',
            'kode_barang',
            'nama_barang',
            'satuan',
            'kategori',
            'keterangan',
            'created_at',
            'updated_at'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Kategori',
            'Keterangan',
            'Tanggal Dibuat',
            'Tanggal Diperbarui'
        ];
    }

    public function map($barang): array
    {
        return [
            $barang->id,
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->satuan,
            $barang->kategori,
            $barang->keterangan ?? '-',
            $barang->created_at ? $barang->created_at->format('d/m/Y H:i') : '-',
            $barang->updated_at ? $barang->updated_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }
} 
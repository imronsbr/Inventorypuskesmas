<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterBarangHabisPakaiTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'HPK001',
                'Kertas A4',
                'Rim',
                'ATK',
                'Kertas untuk keperluan administrasi'
            ],
            [
                'HPK002',
                'Tinta Printer HP',
                'Botol',
                'ATK',
                'Tinta untuk printer HP'
            ],
            [
                'HPK003',
                'Sabun Cuci',
                'Botol',
                'ART',
                'Sabun untuk kebersihan'
            ],
            [
                'HPK004',
                'Pulpen',
                'Pcs',
                'ATK',
                'Pulpen untuk menulis'
            ],
            [
                'HPK005',
                'Piring Plastik',
                'Pcs',
                'Alat Makan',
                'Piring untuk makan'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Kategori',
            'Keterangan'
        ];
    }
} 
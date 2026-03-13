<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterBarangModalTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            [
                '123456789012',
                'Laptop Dell Inspiron',
                'Unit',
                'Elektronik',
                'Laptop untuk keperluan administrasi'
            ],
            [
                '234567890123',
                'Meja Kerja',
                'Unit',
                'Furniture',
                'Meja kerja untuk staff'
            ],
            [
                '345678901234',
                'Mobil Dinas',
                'Unit',
                'Kendaraan',
                'Kendaraan untuk transportasi'
            ],
            [
                '456789012345',
                'Stetoskop',
                'Unit',
                'Alat Medis',
                'Alat untuk pemeriksaan medis'
            ],
            [
                '567890123456',
                'Printer HP',
                'Unit',
                'Elektronik',
                'Printer untuk keperluan cetak'
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
            'A:E' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]
        ];
    }
} 
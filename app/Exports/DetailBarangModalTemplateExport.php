<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailBarangModalTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            [
                '123456789012',
                'SN001234567',
                'Dell',
                'Inspiron 15',
                '2023',
                '1',
                'baik',
                'NIE001',
                '{"nie": "NIE001", "serial_number": "SN001234567", "mac_address": "00:1B:44:11:3A:B7", "warranty": "2025-12-31"}',
                'Laptop untuk keperluan administrasi',
                '15000000',
                '15000000',
                '1'
            ],
            [
                '234567890123',
                'SN002345678',
                'HP',
                'Pavilion 14',
                '2023',
                '2',
                'baik',
                'NIE002',
                '{"nie": "NIE002", "serial_number": "SN002345678", "ip_address": "192.168.1.100", "license": "Windows 11 Pro"}',
                'Laptop untuk keperluan desain',
                '18000000',
                '18000000',
                '1'
            ],
            [
                '345678901234',
                'SN003456789',
                'Canon',
                'Pixma MG2570',
                '2022',
                '3',
                'perbaikan',
                'NIE003',
                '{"nie": "NIE003", "serial_number": "SN003456789", "warranty": "2024-06-30"}',
                'Printer untuk keperluan cetak',
                '2500000',
                '2500000',
                '1'
            ],
            [
                '456789012345',
                'SN004567890',
                'Samsung',
                'Galaxy Tab A',
                '2023',
                '4',
                'baik',
                'NIE004',
                '{"nie": "NIE004", "serial_number": "SN004567890", "mac_address": "00:1B:44:11:3A:B8", "warranty": "2025-12-31"}',
                'Tablet untuk presentasi',
                '8000000',
                '8000000',
                '1'
            ],
            [
                '567890123456',
                'SN005678901',
                'Epson',
                'L3210',
                '2022',
                '5',
                'rusak_berat',
                'NIE005',
                '{"nie": "NIE005", "serial_number": "SN005678901", "warranty": "2024-03-15"}',
                'Printer untuk keperluan cetak dokumen',
                '3000000',
                '3000000',
                '1'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nomor Seri',
            'Merk',
            'Tipe',
            'Tahun Perolehan',
            'Ruang ID',
            'Kondisi',
            'NIE',
            'Atribut (JSON)',
            'Keterangan',
            'Harga',
            'Kapitalisasi',
            'Jumlah'
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
            'A:N' => [
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
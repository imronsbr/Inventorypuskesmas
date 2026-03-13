<?php

namespace App\Exports;

use App\Models\DetailBarangModal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailBarangModalExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return DetailBarangModal::with(['masterBarangModal', 'ruang.puskesmas'])->select([
            'id',
            'kode_barang',
            'nomor_seri',
            'merk',
            'tipe',
            'tahun_perolehan',
            'ruang_id',
            'kondisi',
            'nie',
            'atribut',
            'keterangan',
            'harga',
            'kapitalisasi',
            'jumlah',
            'created_at',
            'updated_at'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
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
            'Jumlah',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];
    }

    public function map($detail): array
    {
        return [
            $detail->id,
            $detail->kode_barang ?? '-',
            $detail->nomor_seri ?? '-',
            $detail->merk ?? '-',
            $detail->tipe ?? '-',
            $detail->tahun_perolehan ?? '-',
            $detail->ruang_id ?? '-',
            $detail->kondisi ?? '-',
            $detail->nie ?? '-',
            $detail->atribut ? json_encode($detail->atribut) : '-',
            $detail->keterangan ?? '-',
            $detail->harga ? number_format($detail->harga, 0, ',', '.') : '-',
            $detail->kapitalisasi ? number_format($detail->kapitalisasi, 0, ',', '.') : '-',
            $detail->jumlah ?? '-',
            $detail->created_at ? $detail->created_at->format('Y-m-d H:i:s') : '-',
            $detail->updated_at ? $detail->updated_at->format('Y-m-d H:i:s') : '-',
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
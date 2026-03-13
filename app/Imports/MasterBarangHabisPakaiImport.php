<?php

namespace App\Imports;

use App\Models\MasterBarangHabisPakai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class MasterBarangHabisPakaiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsErrors
{
    public function model(array $row)
    {
        return new MasterBarangHabisPakai([
            'kode_barang' => $row['kode_barang'],
            'nama_barang' => $row['nama_barang'],
            'satuan' => $row['satuan'],
            'kategori' => $row['kategori'] ?? 'Lainnya',
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_barang' => 'required|string|unique:master_barang_habis_pakais,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'kategori' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_barang.required' => 'Kode Barang wajib diisi.',
            'kode_barang.unique' => 'Kode Barang sudah ada dalam database.',
            'nama_barang.required' => 'Nama Barang wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
        ];
    }
} 
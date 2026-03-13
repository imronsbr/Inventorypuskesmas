<?php

namespace App\Imports;

use App\Models\MasterBarangModal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\Log;

class MasterBarangModalImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        // Log the row data for debugging
        \Log::info('Processing row: ' . json_encode($row));
        
        // Skip if kode_barang is empty
        if (empty($row['kode_barang'])) {
            \Log::info('Skipping row - kode_barang is empty');
            return null;
        }

        // Check if record already exists
        $existing = MasterBarangModal::where('kode_barang', $row['kode_barang'])->first();
        if ($existing) {
            \Log::info('Updating existing record: ' . $row['kode_barang']);
            // Update existing record
            $existing->update([
                'nama_barang' => $row['nama_barang'] ?? $existing->nama_barang,
                'satuan' => $row['satuan'] ?? $existing->satuan,
                'kategori' => $row['kategori'] ?? $existing->kategori,
                'keterangan' => $row['keterangan'] ?? $existing->keterangan,
            ]);
            return null; // Don't create new record
        }

        \Log::info('Creating new record: ' . $row['kode_barang']);
        return new MasterBarangModal([
            'kode_barang' => trim($row['kode_barang']),
            'nama_barang' => trim($row['nama_barang'] ?? ''),
            'satuan' => trim($row['satuan'] ?? ''),
            'kategori' => trim($row['kategori'] ?? 'Lainnya'),
            'keterangan' => trim($row['keterangan'] ?? ''),
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_barang' => [
                'required',
                'string',
                'regex:/^\d{12}$/', // Hanya angka, tepat 12 digit
            ],
            'nama_barang' => 'nullable|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'kategori' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_barang.required' => 'Kode barang harus diisi',
            'kode_barang.regex' => 'Kode barang harus berupa angka dengan tepat 12 digit',
            'kode_barang.unique' => 'Kode barang sudah ada dalam database',
        ];
    }

    public function onError(\Throwable $e)
    {
        Log::error('Excel import error: ' . $e->getMessage());
        Log::error('Excel import error stack trace: ' . $e->getTraceAsString());
    }
} 
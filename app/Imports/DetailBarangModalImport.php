<?php

namespace App\Imports;

use App\Models\DetailBarangModal;
use App\Models\MasterBarangModal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\Log;

class DetailBarangModalImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        // Check if record exists
        $existing = DetailBarangModal::where('kode_barang', $row['kode_barang'])
            ->where('nomor_seri', $row['nomor_seri'] ?? null)
            ->first();

        if ($existing) {
            // Update existing record
            $existing->update([
                'merk' => $row['merk'] ?? $existing->merk,
                'tipe' => $row['tipe'] ?? $existing->tipe,
                'tahun_perolehan' => $row['tahun_perolehan'] ?? $existing->tahun_perolehan,
                'ruang_id' => $row['ruang_id'] ?? $existing->ruang_id,
                'kondisi' => $row['kondisi'] ?? $existing->kondisi,
                'nie' => $row['nie'] ?? $existing->nie,
                'atribut' => $this->parseAtribut($row['atribut'] ?? null),
                'keterangan' => $row['keterangan'] ?? $existing->keterangan,
                'harga' => $row['harga'] ?? $existing->harga,
                'kapitalisasi' => $row['kapitalisasi'] ?? $existing->kapitalisasi,
                'jumlah' => $row['jumlah'] ?? $existing->jumlah,
            ]);
            return null; // Don't create new record
        }

        // Create new record
        return new DetailBarangModal([
            'kode_barang' => trim($row['kode_barang'] ?? ''),
            'nomor_seri' => trim($row['nomor_seri'] ?? ''),
            'merk' => trim($row['merk'] ?? ''),
            'tipe' => trim($row['tipe'] ?? ''),
            'tahun_perolehan' => $row['tahun_perolehan'] ?? null,
            'ruang_id' => $row['ruang_id'] ?? null,
            'kondisi' => trim($row['kondisi'] ?? 'baik'),
            'nie' => trim($row['nie'] ?? ''),
            'atribut' => $this->parseAtribut($row['atribut'] ?? null),
            'keterangan' => trim($row['keterangan'] ?? ''),
            'harga' => $row['harga'] ?? null,
            'kapitalisasi' => $row['kapitalisasi'] ?? null,
            'jumlah' => $row['jumlah'] ?? 1,
        ]);
    }

    private function parseAtribut($atributString)
    {
        if (empty($atributString)) {
            return null;
        }

        // Try to decode JSON
        if (is_string($atributString)) {
            $decoded = json_decode($atributString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // If not valid JSON, return as is
        return $atributString;
    }

    public function rules(): array
    {
        return [
            'kode_barang' => 'required|string|max:255',
            'nomor_seri' => 'nullable|string|max:255',
            'merk' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'tahun_perolehan' => 'nullable|integer',
            'ruang_id' => 'nullable|integer',
            'kondisi' => 'nullable|string|max:255',
            'nie' => 'nullable|string|max:255',
            'atribut' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'harga' => 'nullable|numeric',
            'kapitalisasi' => 'nullable|numeric',
            'jumlah' => 'nullable|integer',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_barang.required' => 'Kode barang harus diisi',
            'kode_barang.regex' => 'Kode barang harus berupa angka dengan tepat 12 digit',
            'kondisi.in' => 'Kondisi harus salah satu dari: baik, perbaikan, rusak_berat',
            'ruang_id.exists' => 'Ruang ID tidak ditemukan dalam database',
        ];
    }

    public function onError(\Throwable $e)
    {
        Log::error('Detail barang modal Excel import error: ' . $e->getMessage());
        Log::error('Detail barang modal Excel import error stack trace: ' . $e->getTraceAsString());
    }
} 
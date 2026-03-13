<?php

/**
 * Script untuk Import Data Master Barang Habis Pakai dari CSV
 * 
 * Cara penggunaan:
 * 1. Simpan file CSV di folder yang sama dengan script ini
 * 2. Sesuaikan nama file CSV di variabel $csvFile
 * 3. Jalankan script: php import_csv_to_master_barang_habis_pakai.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;

// Load Laravel environment
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Konfigurasi
$csvFile = 'my_data.csv'; // Sesuaikan dengan nama file CSV Anda
$csvPath = __DIR__ . '/' . $csvFile;

// Validasi file CSV
if (!file_exists($csvPath)) {
    die("Error: File CSV tidak ditemukan: $csvPath\n");
}

echo "Memulai import data dari file: $csvFile\n";
echo "==========================================\n";

try {
    // Baca file CSV
    $handle = fopen($csvPath, 'r');
    if (!$handle) {
        throw new Exception("Tidak dapat membuka file CSV");
    }

    // Skip header
    $header = fgetcsv($handle);
    if (!$header) {
        throw new Exception("File CSV kosong atau format tidak valid");
    }

    // Validasi header
    $expectedHeaders = ['kode_barang', 'nama_barang', 'spesifikasi', 'satuan', 'kategori', 'harga_satuan', 'keterangan'];
    if ($header !== $expectedHeaders) {
        throw new Exception("Header CSV tidak sesuai. Expected: " . implode(',', $expectedHeaders));
    }

    $imported = 0;
    $updated = 0;
    $errors = 0;
    $rowNumber = 1; // Mulai dari 1 karena sudah skip header

    // Import data baris per baris
    while (($data = fgetcsv($handle)) !== false) {
        $rowNumber++;
        
        try {
            // Validasi data
            if (count($data) !== count($expectedHeaders)) {
                echo "Warning: Baris $rowNumber - Jumlah kolom tidak sesuai\n";
                $errors++;
                continue;
            }

            // Mapping data
            $barang = [
                'kode_barang' => trim($data[0]),
                'nama_barang' => trim($data[1]),
                'spesifikasi' => trim($data[2]),
                'satuan' => trim($data[3]),
                'kategori' => trim($data[4]),
                'harga_satuan' => floatval($data[5]) ?: 0,
                'keterangan' => trim($data[6]),
            ];

            // Validasi data wajib
            if (empty($barang['kode_barang']) || empty($barang['nama_barang']) || 
                empty($barang['satuan']) || empty($barang['kategori'])) {
                echo "Warning: Baris $rowNumber - Data wajib tidak lengkap\n";
                $errors++;
                continue;
            }

            // Cek apakah data sudah ada
            $existing = Capsule::table('master_barang_habis_pakais')
                ->where('kode_barang', $barang['kode_barang'])
                ->first();

            if ($existing) {
                // Update data yang sudah ada
                Capsule::table('master_barang_habis_pakais')
                    ->where('kode_barang', $barang['kode_barang'])
                    ->update([
                        'nama_barang' => $barang['nama_barang'],
                        'spesifikasi' => $barang['spesifikasi'],
                        'satuan' => $barang['satuan'],
                        'kategori' => $barang['kategori'],
                        'harga_satuan' => $barang['harga_satuan'],
                        'keterangan' => $barang['keterangan'],
                        'updated_at' => now(),
                    ]);
                $updated++;
                echo "Updated: {$barang['kode_barang']} - {$barang['nama_barang']}\n";
            } else {
                // Insert data baru
                Capsule::table('master_barang_habis_pakais')->insert([
                    'kode_barang' => $barang['kode_barang'],
                    'nama_barang' => $barang['nama_barang'],
                    'spesifikasi' => $barang['spesifikasi'],
                    'satuan' => $barang['satuan'],
                    'kategori' => $barang['kategori'],
                    'harga_satuan' => $barang['harga_satuan'],
                    'keterangan' => $barang['keterangan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $imported++;
                echo "Imported: {$barang['kode_barang']} - {$barang['nama_barang']}\n";
            }

        } catch (Exception $e) {
            echo "Error pada baris $rowNumber: " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    fclose($handle);

    // Tampilkan hasil
    echo "\n==========================================\n";
    echo "Hasil Import:\n";
    echo "- Data baru: $imported\n";
    echo "- Data diupdate: $updated\n";
    echo "- Error: $errors\n";
    echo "- Total diproses: " . ($imported + $updated + $errors) . "\n";

    // Verifikasi data
    $totalRecords = Capsule::table('master_barang_habis_pakais')->count();
    echo "- Total data di database: $totalRecords\n";

    // Tampilkan sample data terbaru
    echo "\nSample data terbaru:\n";
    $recentData = Capsule::table('master_barang_habis_pakais')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['kode_barang', 'nama_barang', 'satuan', 'kategori', 'harga_satuan']);

    foreach ($recentData as $item) {
        echo "- {$item->kode_barang}: {$item->nama_barang} ({$item->satuan}) - Rp " . number_format($item->harga_satuan, 0, ',', '.') . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nImport selesai!\n"; 
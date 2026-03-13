<?php
/**
 * Script Batch Import CSV - Import banyak file CSV sekaligus
 * 
 * Cara pakai:
 * 1. Simpan semua file CSV di folder database/csv_templates/
 * 2. Jalankan: php batch_import_csv.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Mapping nama file CSV ke nama tabel
$csvToTableMapping = [
    'users.csv' => 'users',
    'master_barang_habis_pakais.csv' => 'master_barang_habis_pakais',
    'master_obats.csv' => 'master_obats',
    'master_barang_modals.csv' => 'master_barang_modals',
    'stok_habis_pakais.csv' => 'stok_habis_pakais',
    'detail_barangs.csv' => 'detail_barangs',
    'detail_barang_modals.csv' => 'detail_barang_modals',
    'detail_pengadaan_barangs.csv' => 'detail_pengadaan_barangs',
    'detail_permintaan_barangs.csv' => 'detail_permintaan_barangs',
    'permintaan_barangs.csv' => 'permintaan_barangs',
    'pengadaan_barangs.csv' => 'pengadaan_barangs',
    'permintaan_barang_logs.csv' => 'permintaan_barang_logs',
    'monitoring_alkes.csv' => 'monitoring_alkes',
    'puskesmas.csv' => 'puskesmas',
    'ruangs.csv' => 'ruangs',
    'jabatans.csv' => 'jabatans',
    'agamas.csv' => 'agamas',
    'pendidikans.csv' => 'pendidikans',
    'roles.csv' => 'roles',
    'pegawais.csv' => 'pegawais',
    'keluargas.csv' => 'keluargas',
    'jabatan_pegawai.csv' => 'jabatan_pegawai',
    'str.csv' => 'str',
    'sip.csv' => 'sip',
];

// Unique keys untuk update
$uniqueKeys = [
    'users' => 'email',
    'master_barang_habis_pakais' => 'kode_barang',
    'master_obats' => 'kode_obat',
    'master_barang_modals' => 'kode_barang',
    'puskesmas' => 'kode',
    'ruangs' => 'kode',
    'jabatans' => 'nama',
    'agamas' => 'nama',
    'pendidikans' => 'nama',
    'pegawais' => 'nip',
    'pengadaan_barangs' => 'nomor_pengadaan',
    'str' => 'nomor_str',
    'sip' => 'nomor_sip',
    'roles' => 'name',
];

echo "=== BATCH IMPORT CSV ===\n";
echo "Mencari file CSV untuk diimport...\n\n";

$totalImported = 0;
$totalUpdated = 0;
$totalErrors = 0;

foreach ($csvToTableMapping as $csvFile => $tableName) {
    $csvPath = __DIR__ . '/../csv_templates/' . $csvFile;
    
    if (!file_exists($csvPath)) {
        echo "⚠️  File tidak ditemukan: $csvFile\n";
        continue;
    }
    
    echo "📁 Processing: $csvFile → $tableName\n";
    
    try {
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            echo "❌ Error: Tidak dapat membuka file $csvFile\n";
            continue;
        }
        
        $header = fgetcsv($handle);
        if (!$header) {
            echo "❌ Error: File $csvFile kosong atau format tidak valid\n";
            continue;
        }
        
        // Validasi kolom menggunakan Laravel Schema
        $columns = Schema::getColumnListing($tableName);
        $missing = array_diff($header, $columns);
        if ($missing) {
            echo "❌ Error: Kolom tidak ada di tabel $tableName: " . implode(', ', $missing) . "\n";
            continue;
        }
        
        $imported = 0;
        $updated = 0;
        $errors = 0;
        $rowNumber = 1;
        
        while (($data = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            if (count($data) !== count($header)) {
                $errors++;
                continue;
            }
            
            $row = array_combine($header, $data);
            foreach ($row as $k => $v) {
                if ($v === '') $row[$k] = null;
            }
            
            try {
                $uniqueKey = $uniqueKeys[$tableName] ?? null;
                
                if ($uniqueKey && isset($row[$uniqueKey]) && $row[$uniqueKey] !== null) {
                    $existing = DB::table($tableName)->where($uniqueKey, $row[$uniqueKey])->first();
                    if ($existing) {
                        DB::table($tableName)->where($uniqueKey, $row[$uniqueKey])->update(array_merge($row, ['updated_at' => now()]));
                        $updated++;
                        continue;
                    }
                }
                
                // Insert baru
                if (in_array('created_at', $columns) && !isset($row['created_at'])) {
                    $row['created_at'] = now();
                }
                if (in_array('updated_at', $columns) && !isset($row['updated_at'])) {
                    $row['updated_at'] = now();
                }
                // Handle deleted_at (soft delete)
                if (in_array('deleted_at', $columns) && !isset($row['deleted_at'])) {
                    $row['deleted_at'] = null; // Set null untuk data aktif
                }
                
                DB::table($tableName)->insert($row);
                $imported++;
                
            } catch (Exception $e) {
                $errors++;
            }
        }
        
        fclose($handle);
        
        echo "✅ $csvFile: Imported=$imported, Updated=$updated, Errors=$errors\n";
        
        $totalImported += $imported;
        $totalUpdated += $updated;
        $totalErrors += $errors;
        
    } catch (Exception $e) {
        echo "❌ Error processing $csvFile: " . $e->getMessage() . "\n";
        $totalErrors++;
    }
}

echo "\n=== HASIL BATCH IMPORT ===\n";
echo "Total Imported: $totalImported\n";
echo "Total Updated: $totalUpdated\n";
echo "Total Errors: $totalErrors\n";
echo "Total Processed: " . ($totalImported + $totalUpdated + $totalErrors) . "\n";

echo "\nBatch import selesai!\n"; 
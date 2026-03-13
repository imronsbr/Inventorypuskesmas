<?php
/**
 * Script generik untuk import data dari CSV ke tabel manapun di database Laravel
 *
 * Cara pakai:
 * 1. Simpan file CSV di folder ini (header = nama kolom tabel)
 * 2. Edit variabel $csvFile dan $tableName di bawah
 * 3. Jalankan: php import_csv_to_table.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// === KONFIGURASI ===
$csvFile = 'my_data.csv'; // Nama file CSV (isi header sesuai kolom tabel)
$tableName = 'master_barang_habis_pakais'; // Nama tabel tujuan
$uniqueKey = null; // Kolom unik untuk update (contoh: 'email', 'kode_barang', 'kode_obat', null=selalu insert)

// === OPSIONAL: SET UNIQUE KEY OTOMATIS UNTUK TABEL TERTENTU ===
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
    'permintaan_barangs' => null, // Tidak ada unique key
    'pengadaan_barangs' => 'nomor_pengadaan',
    'detail_barangs' => null, // Tidak ada unique key
    'detail_barang_modals' => null, // Tidak ada unique key
    'detail_pengadaan_barangs' => null, // Tidak ada unique key
    'detail_permintaan_barangs' => null, // Tidak ada unique key
    'monitoring_alkes' => null, // Tidak ada unique key
    'stok_habis_pakais' => null, // Tidak ada unique key
    'permintaan_barang_logs' => null, // Tidak ada unique key
    'keluargas' => null, // Tidak ada unique key
    'jabatan_pegawai' => null, // Tidak ada unique key (composite unique)
    'str' => 'nomor_str',
    'sip' => 'nomor_sip',
    'roles' => 'name',
    // Tambahkan mapping lain jika perlu
];
if (isset($uniqueKeys[$tableName])) {
    $uniqueKey = $uniqueKeys[$tableName];
}

$csvPath = __DIR__ . '/' . $csvFile;
if (!file_exists($csvPath)) {
    die("Error: File CSV tidak ditemukan: $csvPath\n");
}

echo "\n=== Import CSV ke tabel: $tableName ===\n";
echo "File: $csvFile\n";

try {
    $handle = fopen($csvPath, 'r');
    if (!$handle) throw new Exception("Tidak dapat membuka file CSV");
    $header = fgetcsv($handle);
    if (!$header) throw new Exception("File CSV kosong atau format tidak valid");

    // Ambil kolom tabel dari database
    $columns = Capsule::getSchemaBuilder()->getColumnListing($tableName);
    $missing = array_diff($header, $columns);
    if ($missing) {
        throw new Exception("Kolom berikut tidak ada di tabel: " . implode(', ', $missing));
    }

    $imported = 0; $updated = 0; $errors = 0; $rowNumber = 1;
    while (($data = fgetcsv($handle)) !== false) {
        $rowNumber++;
        if (count($data) !== count($header)) {
            echo "Warning: Baris $rowNumber - Jumlah kolom tidak sesuai\n";
            $errors++; continue;
        }
        $row = array_combine($header, $data);
        // Kosongkan string kosong jadi null
        foreach ($row as $k => $v) { if ($v === '') $row[$k] = null; }
        try {
            if ($uniqueKey && isset($row[$uniqueKey]) && $row[$uniqueKey] !== null) {
                $existing = Capsule::table($tableName)->where($uniqueKey, $row[$uniqueKey])->first();
                if ($existing) {
                    Capsule::table($tableName)->where($uniqueKey, $row[$uniqueKey])->update(array_merge($row, ['updated_at' => now()]));
                    $updated++;
                    echo "Updated: {$row[$uniqueKey]}\n";
                    continue;
                }
            }
            // Insert baru
            if (in_array('created_at', $columns) && !isset($row['created_at'])) $row['created_at'] = now();
            if (in_array('updated_at', $columns) && !isset($row['updated_at'])) $row['updated_at'] = now();
            Capsule::table($tableName)->insert($row);
            $imported++;
            echo "Imported: " . ($uniqueKey ? $row[$uniqueKey] : json_encode($row)) . "\n";
        } catch (Exception $e) {
            echo "Error pada baris $rowNumber: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    fclose($handle);
    echo "\n=== Hasil Import ===\n";
    echo "- Data baru: $imported\n";
    echo "- Data diupdate: $updated\n";
    echo "- Error: $errors\n";
    echo "- Total diproses: " . ($imported + $updated + $errors) . "\n";
    $total = Capsule::table($tableName)->count();
    echo "- Total data di tabel: $total\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\nImport selesai!\n"; 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterBarangHabisPakai;
use App\Models\StokHabisPakai;
use App\Models\Ruang;

class StokHabisPakaiSeeder extends Seeder
{
    public function run(): void
    {
        // Get some master barang and ruang for seeding
        $barangs = MasterBarangHabisPakai::take(5)->get();
        $ruangs = Ruang::take(3)->get();

        if ($barangs->isEmpty() || $ruangs->isEmpty()) {
            return; // Skip if no master barang or ruang
        }

        $periode = date('Y-m');
        
        foreach ($barangs as $barang) {
            $ruang = $ruangs->random();
            
            // Create initial stock
            StokHabisPakai::create([
                'master_barang_habis_pakai_id' => $barang->id,
                'periode' => $periode,
                'stok_awal' => 0,
                'masuk' => rand(10, 50),
                'keluar' => 0,
                'harga_satuan' => $barang->harga_satuan ?: rand(1000, 50000),
                'lokasi_id' => $ruang->id,
                'keterangan' => 'Stok awal bulan ' . $periode,
            ]);

            // Create some stock movements
            for ($i = 1; $i <= rand(2, 5); $i++) {
                $isMasuk = rand(0, 1) == 1;
                
                StokHabisPakai::create([
                    'master_barang_habis_pakai_id' => $barang->id,
                    'periode' => $periode,
                    'stok_awal' => StokHabisPakai::where('master_barang_habis_pakai_id', $barang->id)
                        ->where('periode', $periode)
                        ->latest()
                        ->first()?->stok_akhir ?: 0,
                    'masuk' => $isMasuk ? rand(1, 10) : 0,
                    'keluar' => !$isMasuk ? rand(1, 5) : 0,
                    'harga_satuan' => $barang->harga_satuan ?: rand(1000, 50000),
                    'lokasi_id' => $ruang->id,
                    'keterangan' => $isMasuk ? 'Penambahan stok' : 'Penggunaan stok',
                ]);
            }
        }
    }
} 
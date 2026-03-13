<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MonitoringAlkes;
use App\Models\DetailBarangModal;

class MonitoringAlkesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample detail barang modal
        $detailBarangs = DetailBarangModal::take(10)->get();
        
        if ($detailBarangs->isEmpty()) {
            return; // Skip if no data available
        }

        $kondisis = ['baik', 'rusak ringan', 'rusak berat'];
        $posisis = ['Ruang Poli Umum', 'Ruang Poli Gigi', 'Ruang Laboratorium', 'Ruang Farmasi', 'Ruang Administrasi'];
        
        foreach ($detailBarangs as $detailBarang) {
            // Create multiple monitoring records for each item
            for ($i = 0; $i < rand(1, 3); $i++) {
                MonitoringAlkes::create([
                    'detail_barang_modal_id' => $detailBarang->id,
                    'tanggal_monitor' => now()->subDays(rand(1, 30)),
                    'kondisi' => $kondisis[array_rand($kondisis)],
                    'posisi' => $posisis[array_rand($posisis)],
                    'keterangan' => 'Monitoring rutin bulanan',
                ]);
            }
        }
    }
} 
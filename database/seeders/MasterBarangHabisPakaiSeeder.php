<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterBarangHabisPakai;

class MasterBarangHabisPakaiSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = [
            [
                'kode_barang' => 'ATK-001',
                'nama_barang' => 'Kertas A4 80gsm',
                'spesifikasi' => 'Kertas A4 putih, 80gsm, 500 lembar/rim',
                'satuan' => 'Rim',
                'kategori' => 'ATK',
                'harga_satuan' => 45000,
                'keterangan' => 'Kertas untuk print dan fotokopi',
            ],
            [
                'kode_barang' => 'ATK-002',
                'nama_barang' => 'Pulpen Biru',
                'spesifikasi' => 'Pulpen ballpoint biru, 0.5mm',
                'satuan' => 'Pcs',
                'kategori' => 'ATK',
                'harga_satuan' => 2500,
                'keterangan' => 'Pulpen untuk keperluan kantor',
            ],
            [
                'kode_barang' => 'ART-001',
                'nama_barang' => 'Sabun Cuci Piring',
                'spesifikasi' => 'Sabun cuci piring lemon, 800ml',
                'satuan' => 'Botol',
                'kategori' => 'ART',
                'harga_satuan' => 15000,
                'keterangan' => 'Sabun untuk mencuci peralatan makan',
            ],
            [
                'kode_barang' => 'ART-002',
                'nama_barang' => 'Tissue Gulung',
                'spesifikasi' => 'Tissue toilet 2 ply, 200m',
                'satuan' => 'Roll',
                'kategori' => 'ART',
                'harga_satuan' => 8500,
                'keterangan' => 'Tissue untuk toilet',
            ],
            [
                'kode_barang' => 'ALM-001',
                'nama_barang' => 'Piring Plastik',
                'spesifikasi' => 'Piring plastik putih, diameter 20cm',
                'satuan' => 'Pcs',
                'kategori' => 'Alat Makan',
                'harga_satuan' => 3500,
                'keterangan' => 'Piring untuk keperluan makan',
            ],
            [
                'kode_barang' => 'ALM-002',
                'nama_barang' => 'Sendok Plastik',
                'spesifikasi' => 'Sendok plastik putih, 15cm',
                'satuan' => 'Pcs',
                'kategori' => 'Alat Makan',
                'harga_satuan' => 1500,
                'keterangan' => 'Sendok untuk keperluan makan',
            ],
            [
                'kode_barang' => 'ALL-001',
                'nama_barang' => 'Tabung Reaksi',
                'spesifikasi' => 'Tabung reaksi kaca, 10ml',
                'satuan' => 'Pcs',
                'kategori' => 'Alat Laboratorium',
                'harga_satuan' => 25000,
                'keterangan' => 'Tabung untuk percobaan laboratorium',
            ],
            [
                'kode_barang' => 'ALL-002',
                'nama_barang' => 'Pipet Tetes',
                'spesifikasi' => 'Pipet tetes plastik, 3ml',
                'satuan' => 'Pcs',
                'kategori' => 'Alat Laboratorium',
                'harga_satuan' => 5000,
                'keterangan' => 'Pipet untuk transfer cairan',
            ],
        ];

        foreach ($barangs as $barang) {
            // Check if record exists (including soft deleted)
            $existing = MasterBarangHabisPakai::withTrashed()
                ->where('kode_barang', $barang['kode_barang'])
                ->first();
            
            if ($existing) {
                if ($existing->trashed()) {
                    // If soft deleted, restore and update
                    $existing->restore();
                    $existing->update($barang);
                } else {
                    // If exists and not deleted, just update
                    $existing->update($barang);
                }
            } else {
                // If doesn't exist, create new
                MasterBarangHabisPakai::create($barang);
            }
        }
    }
} 
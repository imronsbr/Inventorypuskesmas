<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterObat;

class MasterObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table first to avoid duplicate entries
        MasterObat::truncate();
        
        $obats = [
            [
                'kode_obat' => 'OB001',
                'nama_obat' => 'Paracetamol',
                'satuan' => 'Tablet',
                'harga_satuan' => 500.00,
                'bentuk' => 'Tablet',
                'komposisi' => 'Paracetamol 500mg',
                'kategori' => 'Analgesik',
                'keterangan' => 'Obat pereda nyeri dan demam',
                'stok' => 50,
            ],
            [
                'kode_obat' => 'OB002',
                'nama_obat' => 'Amoxicillin',
                'satuan' => 'Kapsul',
                'harga_satuan' => 1500.00,
                'bentuk' => 'Kapsul',
                'komposisi' => 'Amoxicillin 500mg',
                'kategori' => 'Antibiotik',
                'keterangan' => 'Antibiotik untuk infeksi bakteri',
                'stok' => 25,
            ],
            [
                'kode_obat' => 'OB003',
                'nama_obat' => 'Ibuprofen',
                'satuan' => 'Tablet',
                'harga_satuan' => 800.00,
                'bentuk' => 'Tablet',
                'komposisi' => 'Ibuprofen 400mg',
                'kategori' => 'Analgesik',
                'keterangan' => 'Obat anti inflamasi non steroid',
                'stok' => 8,
            ],
            [
                'kode_obat' => 'OB004',
                'nama_obat' => 'Omeprazole',
                'satuan' => 'Kapsul',
                'harga_satuan' => 2000.00,
                'bentuk' => 'Kapsul',
                'komposisi' => 'Omeprazole 20mg',
                'kategori' => 'Antasida',
                'keterangan' => 'Obat untuk sakit maag',
                'stok' => 0,
            ],
            [
                'kode_obat' => 'OB005',
                'nama_obat' => 'Cetirizine',
                'satuan' => 'Tablet',
                'harga_satuan' => 1200.00,
                'bentuk' => 'Tablet',
                'komposisi' => 'Cetirizine 10mg',
                'kategori' => 'Antihistamin',
                'keterangan' => 'Obat anti alergi',
                'stok' => 15,
            ],
            [
                'kode_obat' => 'OB006',
                'nama_obat' => 'Metformin',
                'satuan' => 'Tablet',
                'harga_satuan' => 2500.00,
                'bentuk' => 'Tablet',
                'komposisi' => 'Metformin 500mg',
                'kategori' => 'Antidiabetik',
                'keterangan' => 'Obat untuk diabetes melitus',
                'stok' => 5,
            ],
            [
                'kode_obat' => 'OB007',
                'nama_obat' => 'Amlodipine',
                'satuan' => 'Tablet',
                'harga_satuan' => 1800.00,
                'bentuk' => 'Tablet',
                'komposisi' => 'Amlodipine 5mg',
                'kategori' => 'Antihipertensi',
                'keterangan' => 'Obat untuk tekanan darah tinggi',
                'stok' => 0,
            ],
            [
                'kode_obat' => 'OB008',
                'nama_obat' => 'Simvastatin',
                'satuan' => 'Tablet',
                'harga_satuan' => 3000.00,
                'bentuk' => 'Tablet',
                'komposisi' => 'Simvastatin 20mg',
                'kategori' => 'Antilipidemik',
                'keterangan' => 'Obat untuk menurunkan kolesterol',
                'stok' => 30,
            ],
            [
                'kode_obat' => 'OB009',
                'nama_obat' => 'Loratadine',
                'satuan' => 'Tablet',
                'harga_satuan' => 1500.00,
                'bentuk' => 'Tablet',
                'komposisi' => 'Loratadine 10mg',
                'kategori' => 'Antihistamin',
                'keterangan' => 'Obat anti alergi non sedatif',
                'stok' => 12,
            ],
            [
                'kode_obat' => 'OB010',
                'nama_obat' => 'Ranitidine',
                'satuan' => 'Tablet',
                'harga_satuan' => 1200.00,
                'bentuk' => 'Tablet',
                'komposisi' => 'Ranitidine 150mg',
                'kategori' => 'Antasida',
                'keterangan' => 'Obat untuk tukak lambung',
                'stok' => 0,
            ],
        ];

        foreach ($obats as $obat) {
            MasterObat::create($obat);
        }
    }
} 
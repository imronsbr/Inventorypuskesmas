<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterBarangModal;

class MasterBarangModalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = [
            [
                'kode_barang' => 'BM001',
                'nama_barang' => 'Meja Kerja',
                'satuan' => 'Unit',
                'kategori' => 'Furniture',
                'keterangan' => 'Meja kerja untuk staf administrasi',
            ],
            [
                'kode_barang' => 'BM002',
                'nama_barang' => 'Kursi Kantor',
                'satuan' => 'Unit',
                'kategori' => 'Furniture',
                'keterangan' => 'Kursi ergonomis untuk kenyamanan kerja',
            ],
            [
                'kode_barang' => 'BM003',
                'nama_barang' => 'Lemari Arsip',
                'satuan' => 'Unit',
                'kategori' => 'Furniture',
                'keterangan' => 'Lemari untuk menyimpan dokumen dan arsip',
            ],
            [
                'kode_barang' => 'BM004',
                'nama_barang' => 'Komputer Desktop',
                'satuan' => 'Unit',
                'kategori' => 'Elektronik',
                'keterangan' => 'Komputer untuk keperluan administrasi',
            ],
            [
                'kode_barang' => 'BM005',
                'nama_barang' => 'Printer',
                'satuan' => 'Unit',
                'kategori' => 'Elektronik',
                'keterangan' => 'Printer untuk mencetak dokumen',
            ],
            [
                'kode_barang' => 'BM006',
                'nama_barang' => 'Scanner',
                'satuan' => 'Unit',
                'kategori' => 'Elektronik',
                'keterangan' => 'Scanner untuk digitalisasi dokumen',
            ],
            [
                'kode_barang' => 'BM007',
                'nama_barang' => 'AC Split',
                'satuan' => 'Unit',
                'kategori' => 'Pendingin',
                'keterangan' => 'AC untuk ruang kerja',
            ],
            [
                'kode_barang' => 'BM008',
                'nama_barang' => 'Kipas Angin',
                'satuan' => 'Unit',
                'kategori' => 'Pendingin',
                'keterangan' => 'Kipas angin portable',
            ],
            [
                'kode_barang' => 'BM009',
                'nama_barang' => 'Proyektor',
                'satuan' => 'Unit',
                'kategori' => 'Elektronik',
                'keterangan' => 'Proyektor untuk presentasi',
            ],
            [
                'kode_barang' => 'BM010',
                'nama_barang' => 'Whiteboard',
                'satuan' => 'Unit',
                'kategori' => 'Peralatan',
                'keterangan' => 'Papan tulis untuk rapat dan presentasi',
            ],
        ];

        foreach ($barangs as $barang) {
            MasterBarangModal::create($barang);
        }
    }
} 
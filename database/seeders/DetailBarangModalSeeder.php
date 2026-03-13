<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterBarangModal;
use App\Models\DetailBarangModal;
use App\Models\Ruang;

class DetailBarangModalSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing master barang modals
        $masterBarangModals = MasterBarangModal::all();
        $ruangs = Ruang::all();

        if ($masterBarangModals->isEmpty()) {
            $this->command->info('No master barang modals found. Please run MasterBarangModalSeeder first.');
            return;
        }

        if ($ruangs->isEmpty()) {
            $this->command->info('No ruangs found. Please run RuangSeeder first.');
            return;
        }

        foreach ($masterBarangModals as $masterBarang) {
            // Create 1-3 detail items for each master barang
            $itemCount = rand(1, 3);
            
            for ($i = 1; $i <= $itemCount; $i++) {
                $kondisi = $this->getRandomKondisi();
                $ruang = $ruangs->random();
                $atribut = $this->generateAtribut($masterBarang, $i);
                
                DetailBarangModal::create([
                    'kode_barang' => $masterBarang->kode_barang,
                    'nomor_seri' => $this->generateNomorSeri($masterBarang->kode_barang, $i),
                    'merk' => $this->getRandomMerk($masterBarang->kategori),
                    'tipe' => $this->getRandomTipe($masterBarang->kategori),
                    'tahun_perolehan' => rand(2018, 2024),
                    'ruang_id' => $ruang->id,
                    'kondisi' => $kondisi,
                    'nie' => $this->generateNIE(),
                    'atribut' => $atribut,
                    'keterangan' => $this->getKeteranganByKondisi($kondisi),
                    'harga' => rand(1000000, 50000000),
                    'kapitalisasi' => rand(1000000, 50000000),
                    'jumlah' => rand(1, 5),
                ]);
            }
        }

        $this->command->info('Detail barang modal data seeded successfully!');
    }

    private function generateAtribut($masterBarang, $index)
    {
        $baseAtribut = [
            'nie' => 'NIE' . str_pad($index, 3, '0', STR_PAD_LEFT),
            'serial_number' => 'SN' . str_pad($index, 6, '0', STR_PAD_LEFT),
        ];

        // Add specific attributes based on category
        switch (strtolower($masterBarang->kategori)) {
            case 'elektronik':
            case 'komputer':
                $baseAtribut['mac_address'] = sprintf('00:1B:44:11:3A:%02X', $index);
                $baseAtribut['ip_address'] = '192.168.1.' . (100 + $index);
                $baseAtribut['warranty'] = date('Y-m-d', strtotime('+2 years'));
                $baseAtribut['license'] = 'Windows 11 Pro';
                break;
                
            case 'kendaraan':
                $baseAtribut['bpkb'] = 'BPKB' . str_pad($index, 6, '0', STR_PAD_LEFT);
                $baseAtribut['stnk'] = 'STNK' . str_pad($index, 6, '0', STR_PAD_LEFT);
                $baseAtribut['plat_nomor'] = 'AB' . str_pad($index, 4, '0', STR_PAD_LEFT) . 'CD';
                break;
                
            case 'printer':
            case 'peralatan cetak':
                $baseAtribut['warranty'] = date('Y-m-d', strtotime('+1 year'));
                $baseAtribut['cartridge_model'] = 'Cartridge-' . $index;
                break;
                
            case 'tablet':
            case 'mobile device':
                $baseAtribut['imei'] = 'IMEI' . str_pad($index, 10, '0', STR_PAD_LEFT);
                $baseAtribut['warranty'] = date('Y-m-d', strtotime('+2 years'));
                break;
                
            default:
                $baseAtribut['warranty'] = date('Y-m-d', strtotime('+1 year'));
                break;
        }

        return $baseAtribut;
    }

    private function getRandomKondisi()
    {
        $kondisis = [
            DetailBarangModal::KONDISI_BAIK,
            DetailBarangModal::KONDISI_PERBAIKAN,
            DetailBarangModal::KONDISI_RUSAK_BERAT,
        ];
        
        // 70% chance baik, 20% chance perbaikan, 10% chance rusak berat
        $rand = rand(1, 100);
        if ($rand <= 70) {
            return DetailBarangModal::KONDISI_BAIK;
        } elseif ($rand <= 90) {
            return DetailBarangModal::KONDISI_PERBAIKAN;
        } else {
            return DetailBarangModal::KONDISI_RUSAK_BERAT;
        }
    }

    private function generateNomorSeri($kodeBarang, $index)
    {
        return $kodeBarang . '-' . str_pad($index, 3, '0', STR_PAD_LEFT);
    }

    private function getRandomMerk($kategori)
    {
        $merks = [
            'Elektronik' => ['Dell', 'HP', 'Lenovo', 'Samsung', 'LG', 'Sony'],
            'Furniture' => ['IKEA', 'Informa', 'ACE', 'Furniture Indonesia'],
            'Kendaraan' => ['Toyota', 'Honda', 'Suzuki', 'Mitsubishi'],
            'Alat Medis' => ['Omron', 'GE Healthcare', 'Philips', 'Siemens'],
            'Lainnya' => ['Generic', 'Local Brand', 'Imported'],
        ];

        $availableMerks = $merks[$kategori] ?? $merks['Lainnya'];
        return $availableMerks[array_rand($availableMerks)];
    }

    private function getRandomTipe($kategori)
    {
        $tipes = [
            'Elektronik' => ['Inspiron', 'Pavilion', 'ThinkPad', 'Galaxy', 'Vivobook'],
            'Furniture' => ['Office Chair', 'Work Desk', 'Storage Cabinet', 'Meeting Table'],
            'Kendaraan' => ['Avanza', 'Brio', 'Ertiga', 'Xpander'],
            'Alat Medis' => ['Blood Pressure Monitor', 'Thermometer', 'Stethoscope', 'ECG Machine'],
            'Lainnya' => ['Standard Model', 'Premium Model', 'Basic Model'],
        ];

        $availableTipes = $tipes[$kategori] ?? $tipes['Lainnya'];
        return $availableTipes[array_rand($availableTipes)];
    }

    private function generateNIE()
    {
        return 'NIE-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function getKeteranganByKondisi($kondisi)
    {
        $keterangans = [
            DetailBarangModal::KONDISI_BAIK => [
                'Kondisi sangat baik, siap digunakan',
                'Fungsional dan terawat dengan baik',
                'Tidak ada kerusakan yang signifikan',
                'Sesuai standar operasional',
            ],
            DetailBarangModal::KONDISI_PERBAIKAN => [
                'Memerlukan perbaikan minor',
                'Ada kerusakan ringan yang perlu diperbaiki',
                'Perlu maintenance rutin',
                'Masih bisa digunakan dengan batasan',
            ],
            DetailBarangModal::KONDISI_RUSAK_BERAT => [
                'Kerusakan berat, tidak bisa digunakan',
                'Memerlukan perbaikan besar atau penggantian',
                'Tidak aman untuk digunakan',
                'Kondisi kritis, perlu evaluasi',
            ],
        ];

        $availableKeterangans = $keterangans[$kondisi] ?? ['Kondisi tidak diketahui'];
        return $availableKeterangans[array_rand($availableKeterangans)];
    }
}

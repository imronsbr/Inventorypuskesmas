<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterBarangHabisPakai;
use App\Models\MasterBarangModal;
use App\Models\MasterObat;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class MasterBarangSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Seed MasterBarangHabisPakai
        $kategorisHabisPakai = ['ATK', 'ART', 'Alat Makan', 'Alat Laboratorium', 'Lainnya'];
        for ($i = 1; $i <= 20; $i++) {
            MasterBarangHabisPakai::create([
                'kode_barang' => 'HPK' . sprintf('%03d', $i),
                'nama_barang' => ucfirst($faker->words(2, true)),
                'spesifikasi' => $faker->sentence(),
                'satuan' => $faker->randomElement(['Pcs', 'Box', 'Lusin', 'Kg', 'Liter']),
                'kategori' => $faker->randomElement($kategorisHabisPakai),
                'harga_satuan' => $faker->numberBetween(1000, 100000),
                'keterangan' => $faker->sentence(),
            ]);
        }

        // Seed MasterBarangModal
        $kategorisModal = ['Furniture', 'Elektronik', 'Kendaraan', 'Bangunan', 'Lainnya'];
        for ($i = 1; $i <= 15; $i++) {
            MasterBarangModal::create([
                'kode_barang' => 'MDL' . sprintf('%03d', $i),
                'nama_barang' => ucfirst($faker->words(2, true)),
                'satuan' => $faker->randomElement(['Unit', 'Set', 'Buah']),
                'kategori' => $faker->randomElement($kategorisModal),
                'keterangan' => $faker->sentence(),
            ]);
        }

        // Seed MasterObat
        $kategorisObat = ['Analgesik', 'Antibiotik', 'Antasida', 'Antihistamin', 'Antidiabetik'];
        $bentuks = ['Tablet', 'Kapsul', 'Sirup', 'Salep', 'Injeksi'];
        for ($i = 1; $i <= 15; $i++) {
            MasterObat::create([
                'kode_obat' => 'OBT' . sprintf('%03d', $i),
                'nama_obat' => ucfirst($faker->words(2, true)),
                'satuan' => $faker->randomElement(['Tablet', 'Kapsul', 'Botol', 'Tube']),
                'harga_satuan' => $faker->numberBetween(500, 50000),
                'bentuk' => $faker->randomElement($bentuks),
                'komposisi' => $faker->sentence(),
                'kategori' => $faker->randomElement($kategorisObat),
                'keterangan' => $faker->sentence(),
            ]);
        }
    }
}

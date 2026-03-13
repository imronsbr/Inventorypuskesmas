<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            JabatanSeeder::class,
            PuskesmasSeeder::class,
            RuangSeeder::class,
            MasterBarangSeeder::class,
            MasterBarangHabisPakaiSeeder::class,
            MasterBarangModalSeeder::class,
            DetailBarangModalSeeder::class,
            MasterObatSeeder::class,
            MonitoringAlkesSeeder::class,
            StokHabisPakaiSeeder::class,
            PermintaanBarangSeeder::class,
            PegawaiSeeder::class,
        ]);
    }
}

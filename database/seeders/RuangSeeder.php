<?php

namespace Database\Seeders;

use App\Models\Ruang;
use App\Models\Puskesmas;
use Illuminate\Database\Seeder;

class RuangSeeder extends Seeder
{
    public function run()
    {
        // Cari puskesmas Kebayoran Lama
        $puskesmas = Puskesmas::where('nama', 'Puskesmas Kebayoran Lama')->first();
        if ($puskesmas) {
            Ruang::updateOrCreate([
                'puskesmas_id' => $puskesmas->id,
                'nama' => 'Ruang Umum',
            ], [
                'kode' => 'RU-UMUM',
            ]);
        }
    }
}

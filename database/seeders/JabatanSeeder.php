<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            [
                'nama' => 'Penanggung Jawab Barang',
                'parent_id' => null,
            ],
            [
                'nama' => 'PPTK',
                'parent_id' => null,
            ],
            [
                'nama' => 'Perencana',
                'parent_id' => null,
            ],
            [
                'nama' => 'Kepala Puskesmas',
                'parent_id' => null,
            ],
            [
                'nama' => 'Kepala Tata Usaha',
                'parent_id' => null,
            ],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::updateOrCreate(
                ['nama' => $jabatan['nama']],
                $jabatan
            );
        }
    }
} 
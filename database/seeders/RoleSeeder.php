<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'superuser',
            'admin',
            'petugas',
            'kepala',
            'penanggung_jawab',
            'penanggung_jawab_barang_habis_pakai',
            'perencana',
            'pptk',
            'bendahara',
            'operator',
            'staf',
            // Tambahkan role lain sesuai kebutuhan
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}

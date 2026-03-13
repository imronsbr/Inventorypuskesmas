<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Puskesmas;

class PuskesmasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Puskesmas Kebayoran Lama',
            'Puskesmas Pembantu Pondok Pinang',
            'Puskesmas Kebayoran Lama Utara',
            'Puskesmas Cipulir I',
            'Puskesmas Cipulir II',
            'Puskesmas Grogol Utara 2',
        ];
        foreach ($data as $i => $nama) {
            $kode = 'PKM' . str_pad($i+1, 3, '0', STR_PAD_LEFT);
            Puskesmas::updateOrCreate(['nama' => $nama], ['kode' => $kode]);
        }
    }
}

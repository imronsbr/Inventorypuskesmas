<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Agama;
use App\Models\Pendidikan;
use App\Models\Jabatan;
use App\Models\Str;
use App\Models\Sip;
use App\Models\Keluarga;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Create agama data
        $agamas = [
            'Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'
        ];
        foreach ($agamas as $agama) {
            Agama::firstOrCreate(['nama' => $agama]);
        }

        // Create pendidikan data
        $pendidikans = [
            'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'
        ];
        foreach ($pendidikans as $pendidikan) {
            Pendidikan::firstOrCreate(['nama' => $pendidikan]);
        }

        // Create jabatan data
        $jabatans = [
            'Dokter Umum', 'Dokter Spesialis', 'Perawat', 'Bidan', 'Ahli Gizi', 
            'Sanitarian', 'Analis Kesehatan', 'Admin', 'Kepala Puskesmas',
            'Penanggung Jawab Barang', 'PPTK', 'Perencana', 'Kepala Tata Usaha'
        ];
        foreach ($jabatans as $jabatan) {
            Jabatan::firstOrCreate(['nama' => $jabatan]);
        }

        // Get existing users and create employee data for them
        $users = User::all();
        
        foreach ($users as $user) {
            // Create employee data
            $pegawai = Pegawai::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'nip' => $faker->unique()->numerify('19##########'),
                    'unit_kerja' => 'Puskesmas ' . $faker->city,
                    'agama_id' => Agama::inRandomOrder()->first()->id,
                    'pendidikan_id' => Pendidikan::inRandomOrder()->first()->id,
                    'status_pegawai' => $faker->randomElement(['pns', 'cpns', 'non_pns', 'pjlp']),
                    'jenis_kontrak' => $faker->randomElement(['tetap', 'kontrak']),
                    'nik' => $faker->unique()->numerify('##################'),
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $faker->dateTimeBetween('-50 years', '-20 years'),
                    'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                    'golongan' => $faker->randomElement(['II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d']),
                    'npwp' => $faker->unique()->numerify('##.###.###.#-###.###'),
                    'no_hp' => $faker->phoneNumber,
                    'alamat' => $faker->address,
                    'tmt' => $faker->dateTimeBetween('-10 years', '-1 year'),
                    'tmt_pensiun' => $faker->dateTimeBetween('+10 years', '+20 years'),
                    'keterangan' => $faker->optional()->sentence,
                ]
            );

            // Create jabatan relationships
            $jabatan = Jabatan::inRandomOrder()->first();
            $pegawai->jabatans()->syncWithoutDetaching([
                $jabatan->id => [
                    'tanggal_mulai' => $faker->dateTimeBetween('-5 years', '-1 year'),
                    'tanggal_selesai' => $faker->optional()->dateTimeBetween('+1 year', '+5 years'),
                ]
            ]);

            // Assign approval roles to specific users
            if ($user->email === 'admin@example.com') {
                $approvalJabatan = Jabatan::where('nama', 'Penanggung Jawab Barang')->first();
                if ($approvalJabatan) {
                    $pegawai->jabatans()->syncWithoutDetaching([
                        $approvalJabatan->id => [
                            'tanggal_mulai' => now()->subYear(),
                            'tanggal_selesai' => null, // Active
                        ]
                    ]);
                }
            }

            if ($user->email === 'user@example.com') {
                $approvalJabatan = Jabatan::where('nama', 'PPTK')->first();
                if ($approvalJabatan) {
                    $pegawai->jabatans()->syncWithoutDetaching([
                        $approvalJabatan->id => [
                            'tanggal_mulai' => now()->subYear(),
                            'tanggal_selesai' => null, // Active
                        ]
                    ]);
                }
            }

            // Create STR data (for medical personnel)
            if (in_array($jabatan->nama, ['Dokter Umum', 'Dokter Spesialis', 'Perawat', 'Bidan'])) {
                Str::create([
                    'pegawai_id' => $pegawai->id,
                    'nomor_str' => $faker->unique()->numerify('STR-########'),
                    'tanggal_terbit' => $faker->dateTimeBetween('-5 years', '-1 year'),
                    'tanggal_berakhir' => $faker->dateTimeBetween('+1 year', '+5 years'),
                ]);
            }

            // Create SIP data (for medical personnel)
            if (in_array($jabatan->nama, ['Dokter Umum', 'Dokter Spesialis'])) {
                Sip::create([
                    'pegawai_id' => $pegawai->id,
                    'nomor_sip' => $faker->unique()->numerify('SIP-########'),
                    'tempat_praktik' => $faker->company,
                    'tanggal_terbit' => $faker->dateTimeBetween('-5 years', '-1 year'),
                    'tanggal_berakhir' => $faker->dateTimeBetween('+1 year', '+5 years'),
                ]);
            }

            // Create family data
            $familyMembers = $faker->numberBetween(1, 4);
            for ($i = 0; $i < $familyMembers; $i++) {
                Keluarga::create([
                    'pegawai_id' => $pegawai->id,
                    'nama' => $faker->name,
                    'hubungan' => $faker->randomElement(['Suami', 'Istri', 'Anak', 'Ayah', 'Ibu']),
                    'tanggal_lahir' => $faker->dateTimeBetween('-60 years', '-1 year'),
                    'pekerjaan' => $faker->jobTitle,
                ]);
            }
        }

        $this->command->info('Employee data seeded successfully!');
    }
}

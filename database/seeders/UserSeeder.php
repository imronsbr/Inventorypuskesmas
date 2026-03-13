<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        // Pastikan role 'admin' sudah ada
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // Pastikan role 'superuser' sudah ada
        $superuserRole = Role::firstOrCreate(['name' => 'superuser']);
        // Buat 10 user random dengan role admin
        for ($i = 0; $i < 10; $i++) {
            User::updateOrCreate(
                [ 'email' => $faker->unique()->safeEmail ],
                [
                    'name' => $faker->name,
                    'password' => Hash::make('P@ssw0rd'),
                    'role_id' => $adminRole->id,
                ]
            );
        }
        // Buat user admin
        User::updateOrCreate(
            [ 'email' => 'admin@example.com' ],
            [
                'name' => 'admin',
                'password' => Hash::make('P@ssw0rd'),
                'role_id' => $adminRole->id,
            ]
        );
        // Buat user superuser
        User::updateOrCreate(
            [ 'email' => 'superuser@example.com' ],
            [
                'name' => 'Superuser',
                'password' => Hash::make('supersecret'),
                'nrk' => '000000',
                'role_id' => $superuserRole->id,
            ]
        );
    }
}

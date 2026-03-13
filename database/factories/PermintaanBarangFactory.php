<?php

namespace Database\Factories;

use App\Models\PermintaanBarang;
use App\Models\MasterBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermintaanBarangFactory extends Factory
{
    protected $model = PermintaanBarang::class;

    public function definition()
    {
        return [
            'master_barang_id' => MasterBarang::factory(),
            'jumlah' => $this->faker->numberBetween(1, 100),
            'tanggal_pesanan' => $this->faker->date(),
            'status_acc' => $this->faker->randomElement(['belum', 'acc']),
        ];
    }
}

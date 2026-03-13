<?php

namespace Database\Factories;

use App\Models\DetailBarang;
use App\Models\MasterBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailBarangFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DetailBarang::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'master_barang_id' => MasterBarang::inRandomOrder()->value('id'), // Ambil ID dari MasterBarang secara acak
            'kode' => $this->faker->unique()->numerify('BM####'), // Kode barang modal
            'nama' => $this->faker->word(),
            'jenis' => 'barang_modal',
            'status_summary' => 'Baik (10), Rusak Ringan (2)', // Contoh status summary
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

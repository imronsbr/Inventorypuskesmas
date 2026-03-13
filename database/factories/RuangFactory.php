<?php

namespace Database\Factories;

use App\Models\Ruang;
use Illuminate\Database\Eloquent\Factories\Factory;

class RuangFactory extends Factory
{
    protected $model = Ruang::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->word(),
            'kode' => $this->faker->unique()->numerify('RU###'),
            'puskesmas_id' => 1,
        ];
    }
}

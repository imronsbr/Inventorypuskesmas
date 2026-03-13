<?php

namespace Database\Factories;

use App\Models\Puskesmas;
use Illuminate\Database\Eloquent\Factories\Factory;

class PuskesmasFactory extends Factory
{
    protected $model = Puskesmas::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->company(),
            'kode' => $this->faker->unique()->numerify('PK###'),
            'alamat' => $this->faker->address(),
        ];
    }
}

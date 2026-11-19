<?php

namespace Database\Factories;

use App\Models\Tenan;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenanFactory extends Factory
{
    protected $model = Tenan::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->unique()->company(),
            'keterangan' => fake()->optional()->sentence(4),
        ];
    }
}

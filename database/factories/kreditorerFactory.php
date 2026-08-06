<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kreditorer;

class KreditorerFactory extends Factory
{
    protected $model = Kreditorer::class;

    public function definition(): array
    {
        return [
            'navn' => $this->faker->company,
            'lotusID' => $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}

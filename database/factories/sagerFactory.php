<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sager;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SagerFactory extends Factory
{
    protected $model = Sager::class;

    public function definition()
    {
        return [
            'sagsnr' => $this->faker->unique()->numberBetween(100000, 999999),
            'afsluttet' => $this->faker->boolean(30) ? Carbon::now()->subDays(rand(1, 60)) : null,
            'faktureret' => $this->faker->boolean(40) ? Carbon::now()->subDays(rand(10, 60)) : null,
            'betalt' => $this->faker->boolean(30) ? Carbon::now()->subDays(rand(1, 20)) : null,
            'fakturadato' => $this->faker->boolean(60) ? Carbon::now()->subDays(rand(20, 90)) : null,
            'modtaget' => Carbon::now()->subDays(rand(60, 200)),
            'senesterapport' => Carbon::now()->subDays(rand(10, 50)),
            'opgivet' => $this->faker->boolean(10) ? Carbon::now()->subDays(rand(30, 200)) : null,
            'hovedstol' => $this->faker->randomFloat(2, 1000, 25000),
            'renter' => $this->faker->randomFloat(2, 0, 3000),
            'gebyr' => $this->faker->randomFloat(2, 0, 1000),
            'ialt' => $this->faker->randomFloat(2, 1000, 30000),
            'startgebyr' => $this->faker->randomFloat(2, 0, 800),
            'restgaeld' => $this->faker->randomFloat(2, 0, 15000),
            'restgaeld_dkg' => $this->faker->randomFloat(2, 0, 15000),
            'afdragsordning' => $this->faker->boolean(),
            'indbetalt' => $this->faker->randomFloat(2, 0, 5000),
            'mdlydelse' => $this->faker->randomFloat(2, 100, 2000),
            'n_mdlydelse' => $this->faker->randomFloat(2, 100, 2000),
            'stelnr' => Str::upper(Str::random(10)),
            'aktiv' => $this->faker->boolean(90),
            'fakturanr' => 'FAK-' . $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}

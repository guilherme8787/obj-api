<?php

namespace Database\Factories;

use App\Models\Conta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conta>
 */
class ContaFactory extends Factory
{
    protected $model = Conta::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_conta' => $this->faker->unique()->numberBetween(100000, 999999),
            'saldo' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }

    public function withAttributes()
    {
        return $this->state(function (array $attributes) {
            return array_merge([
                'numero_conta' => $this->faker->unique()->numberBetween(100000, 999999),
                'saldo' => $this->faker->randomFloat(2, 0, 10000),
            ], $attributes);
        });
    }
}

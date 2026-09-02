<?php

namespace Database\Factories;

use App\Models\BonsaiType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BonsaiType>
 */
class BonsaiTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
        ];
    }
}

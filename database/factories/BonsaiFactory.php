<?php

namespace Database\Factories;

use App\Models\Bonsai;
use App\Models\BonsaiType;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class BonsaiFactory extends Factory
{
    protected $model = Bonsai::class;

    public function definition(): array
    {
        return [
            'participant_id' => Participant::factory(),
            'bonsai_type_id' => BonsaiType::factory(),
            'size' => $this->faker->randomElement(['Small', 'Medium', 'Large', 'Mame', 'Shito', 'Extra Large']),
            'class' => $this->faker->randomElement(['Jadi', 'Prospek']),
            'status' => $this->faker->randomElement(['Peserta', 'Pemenang']),
            'predicate' => null,
            'photo' => 'test-photo.jpg',
            'description' => $this->faker->paragraph(),
        ];
    }

    public function peserta(): self
    {
        return $this->state([
            'status' => 'Peserta',
            'predicate' => null,
        ]);
    }

    public function pemenang(): self
    {
        return $this->state([
            'status' => 'Pemenang',
            'predicate' => $this->faker->randomElement(['Juara 1', 'Juara 2', 'Juara 3']),
        ]);
    }
}

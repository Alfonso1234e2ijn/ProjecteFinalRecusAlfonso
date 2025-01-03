<?php

namespace Database\Factories;

use App\Models\Urating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class URatingFactory extends Factory
{
    protected $model = Urating::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'rater_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
        ];
    }
}

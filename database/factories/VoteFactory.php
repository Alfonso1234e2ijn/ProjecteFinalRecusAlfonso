<?php

namespace Database\Factories;

use App\Models\Vote;
use App\Models\User;
use App\Models\Response;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement([1, 0]),
            'user_id' => User::factory(),
            'response_id' => Response::factory(),
        ];
    }
}

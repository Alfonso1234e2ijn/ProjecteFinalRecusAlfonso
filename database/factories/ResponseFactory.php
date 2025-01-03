<?php

namespace Database\Factories;

use App\Models\Response;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResponseFactory extends Factory
{
    protected $model = Response::class;

    public function definition()
    {
        return [
            'content' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'thread_id' => Thread::factory(),
        ];
    }
}

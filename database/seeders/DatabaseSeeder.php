<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Thread;
use App\Models\Response;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory(10)->create();
        Thread::factory(10)->create();
        Response::factory(10)->create();
    }
}

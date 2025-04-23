<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 users
        User::factory(5)->create()->each(function ($user) {
            // Create 10 tasks for each user
            Task::factory(10)->create([
                'user_id' => $user->id,
            ]);

            // Create additional tasks with specific statuses
            Task::factory(3)->pending()->create([
                'user_id' => $user->id,
            ]);
            
            Task::factory(3)->inProgress()->create([
                'user_id' => $user->id,
            ]);
            
            Task::factory(3)->completed()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
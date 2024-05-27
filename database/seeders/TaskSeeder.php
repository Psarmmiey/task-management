<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 tasks
        $users = User::all();

        if ($users->count() === 0) {
            throw new \UnexpectedValueException('No user found');
        }

        foreach($users as $user) {
            $factory = Task::factory();

            $factory
                ->count(10)
                ->create([
                    'author_type' => User::class,
                    'author_id' => $user->getkey(),
                ]);
        }
    }
}

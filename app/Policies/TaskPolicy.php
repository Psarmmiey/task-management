<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        return $task->author_id === $user->id && $task->author_type === $user::class;
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->author_id === $user->id && $task->author_type === $user::class;
    }
}

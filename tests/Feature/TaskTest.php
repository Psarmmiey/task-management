<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_tasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
    }

    public function test_user_can_create_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('tasks.store'), [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
            'due_date' => '2024-05-28'
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_update_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = $user->tasks()->create([
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
            'due_date' => '2021-12-12',
        ]);

        $response = $this->put(route('tasks.update', $task), [
            'title' => 'Updated Task',
            'description' => 'Updated task description',
            'status' => 'completed',
            'due_date' => '2021-12-12',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_update_task_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = $user->tasks()->create([
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
            'due_date' => '2021-12-12',
        ]);

        $response = $this->put(route('tasks.updateStatus', $task), [
            'status' => 'completed',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_delete_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = $user->tasks()->create([
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
            'due_date' => '2021-12-12',
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertStatus(200);
    }
}

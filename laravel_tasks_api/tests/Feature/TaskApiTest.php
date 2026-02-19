<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'First task',
            'description' => 'Task description',
            'status' => 'new',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'title' => 'First task',
                'description' => 'Task description',
                'status' => 'new',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'First task',
            'status' => 'new',
        ]);
    }

    public function test_can_list_tasks(): void
    {
        Task::factory()->create(['title' => 'Task A']);
        Task::factory()->create(['title' => 'Task B']);

        $response = $this->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['title' => 'Task A'])
            ->assertJsonFragment(['title' => 'Task B']);
    }

    public function test_can_show_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $task->id,
                'title' => $task->title,
            ]);
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Old title',
            'description' => 'Old description',
            'status' => 'new',
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'status' => 'done',
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'title' => 'Updated title',
                'description' => 'Updated description',
                'status' => 'done',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated title',
            'status' => 'done',
        ]);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}

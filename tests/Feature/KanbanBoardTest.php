<?php

namespace Tests\Feature;

use App\Livewire\ProjectBoard;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KanbanBoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $user = User::first();
        $this->actingAs($user);
    }

    public function test_can_render_project_board_component(): void
    {
        Livewire::test(ProjectBoard::class)
            ->assertStatus(200)
            ->assertSee('Linear Next-Gen Product Launch');
    }

    public function test_can_reorder_task_and_update_status(): void
    {
        $task = Task::first();

        Livewire::test(ProjectBoard::class)
            ->call('updateTaskOrder', $task->id, 'done', 0);

        $this->assertEquals('done', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_can_duplicate_task(): void
    {
        $task = Task::first();

        Livewire::test(ProjectBoard::class)
            ->call('duplicateTask', $task->id);

        $this->assertDatabaseHas('tasks', [
            'title' => "{$task->title} (Copy)",
        ]);
    }
}

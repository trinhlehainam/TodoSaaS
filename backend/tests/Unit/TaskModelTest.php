<?php

namespace Tests\Unit;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test task creation with all fields.
     */
    public function test_can_create_task_with_all_fields(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $task = Task::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => TaskStatus::Pending->value,
            'priority' => TaskPriority::High->value,
            'due_date' => now()->addDays(7),
        ]);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals(TaskStatus::Pending, $task->status);
        $this->assertEquals(TaskPriority::High, $task->priority);
    }

    /**
     * Test task relationships.
     */
    public function test_task_belongs_to_team_and_user(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->forTeam($team)->assignedTo($user)->create();

        $this->assertInstanceOf(Team::class, $task->team);
        $this->assertEquals($team->id, $task->team->id);

        $this->assertInstanceOf(User::class, $task->assignee);
        $this->assertEquals($user->id, $task->assignee->id);
    }

    /**
     * Test task status scopes.
     */
    public function test_task_status_scopes(): void
    {
        $pendingTask = Task::factory()->pending()->create();
        $inProgressTask = Task::factory()->inProgress()->create();
        $completedTask = Task::factory()->completed()->create();

        $this->assertEquals(1, Task::pending()->count());
        $this->assertEquals(1, Task::inProgress()->count());
        $this->assertEquals(1, Task::completed()->count());

        $this->assertTrue(Task::pending()->first()->is($pendingTask));
        $this->assertTrue(Task::inProgress()->first()->is($inProgressTask));
        $this->assertTrue(Task::completed()->first()->is($completedTask));
    }

    /**
     * Test overdue scope and isOverdue method.
     */
    public function test_overdue_tasks(): void
    {
        $overdueTask = Task::factory()->overdue()->create();
        $futureTask = Task::factory()->pending()->create([
            'due_date' => now()->addDays(7),
        ]);

        $this->assertEquals(1, Task::overdue()->count());
        $this->assertTrue($overdueTask->isOverdue());
        $this->assertFalse($futureTask->isOverdue());
    }

    /**
     * Test mark as completed functionality.
     */
    public function test_mark_task_as_completed(): void
    {
        $task = Task::factory()->pending()->create();

        $this->assertNull($task->completed_at);
        $this->assertEquals(TaskStatus::Pending, $task->status);

        $task->markAsCompleted();
        $task->refresh();

        $this->assertNotNull($task->completed_at);
        $this->assertEquals(TaskStatus::Completed, $task->status);
    }

    /**
     * Test team has many tasks relationship.
     */
    public function test_team_has_many_tasks(): void
    {
        $team = Team::factory()->create();
        $tasks = Task::factory()->count(3)->forTeam($team)->create();

        $this->assertEquals(3, $team->tasks()->count());
        $this->assertTrue($team->tasks->contains($tasks->first()));
    }

    /**
     * Test user has many assigned tasks.
     */
    public function test_user_has_many_assigned_tasks(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $tasks = Task::factory()->count(2)->forTeam($team)->assignedTo($user)->create();

        $this->assertEquals(2, $user->assignedTasks()->count());
        $this->assertEquals(2, $user->tasksInTeam($team)->count());
    }
}

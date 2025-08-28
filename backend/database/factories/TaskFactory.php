<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::cases());
        $completedAt = $status === TaskStatus::Completed
            ? fake()->dateTimeBetween('-30 days', 'now')
            : null;

        return [
            'team_id' => Team::factory(),
            'user_id' => fake()->boolean(80) ? User::factory() : null,
            'title' => fake()->sentence(4),
            'description' => fake()->optional(0.7)->paragraph(),
            'status' => $status->value,
            'priority' => fake()->randomElement(TaskPriority::cases())->value,
            'due_date' => fake()->optional(0.6)->dateTimeBetween('now', '+30 days'),
            'completed_at' => $completedAt,
        ];
    }

    /**
     * Indicate that the task is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Pending->value,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::InProgress->value,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Completed->value,
            'completed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the task is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Cancelled->value,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::High->value,
        ]);
    }

    /**
     * Indicate that the task is low priority.
     */
    public function lowPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::Low->value,
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Pending->value,
            'due_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is assigned to a user.
     */
    public function assignedTo(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the task belongs to a team.
     */
    public function forTeam(Team $team): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team->id,
        ]);
    }

    /**
     * Indicate that the task is unassigned.
     */
    public function unassigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }
}

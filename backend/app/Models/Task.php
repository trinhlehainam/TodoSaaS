<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static Builder pending()
 * @method static Builder inProgress()
 * @method static Builder completed()
 * @method static Builder highPriority()
 * @method static Builder overdue()
 * @method static Task create(array $attributes = [])
 * @method static Task|null find($id, $columns = ['*'])
 * @method static Task findOrFail($id, $columns = ['*'])
 * @method static Task updateOrCreate(array $attributes, array $values = [])
 */
class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the team that owns the task.
     *
     * @return BelongsTo<Team, Task>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user assigned to the task.
     *
     * @return BelongsTo<User, Task>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to only include pending tasks.
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::Pending);
    }

    /**
     * Scope a query to only include in-progress tasks.
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::InProgress);
    }

    /**
     * Scope a query to only include completed tasks.
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::Completed);
    }

    /**
     * Scope a query to only include high priority tasks.
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->where('priority', TaskPriority::High);
    }

    /**
     * Scope a query to only include overdue tasks.
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    /**
     * Check if the task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date &&
               ! $this->completed_at &&
               $this->due_date->isPast();
    }

    /**
     * Check if the task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Completed;
    }

    /**
     * Mark the task as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark the task as in progress.
     */
    public function markAsInProgress(): void
    {
        $this->update([
            'status' => TaskStatus::InProgress,
        ]);
    }

    /**
     * Cancel the task.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => TaskStatus::Cancelled,
        ]);
    }
}

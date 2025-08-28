<?php

namespace App\Models;

use App\Enums\TeamRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'owner_id',
        'personal_team',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->using(TeamMember::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function hasUser(User $user): bool
    {
        return $this->members->contains($user) || $this->owner->is($user);
    }

    public function userRole(User $user): ?TeamRole
    {
        if ($this->owner->is($user)) {
            return TeamRole::Owner;
        }

        $member = $this->teamMembers()->where('user_id', $user->id)->first();

        return $member ? TeamRole::from($member->role) : null;
    }

    public function addMember(User $user, TeamRole $role = TeamRole::Member): void
    {
        $this->members()->attach($user, ['role' => $role->value]);
    }

    public function removeMember(User $user): void
    {
        $this->members()->detach($user);
    }

    public function updateMemberRole(User $user, TeamRole $role): void
    {
        $this->members()->updateExistingPivot($user->id, ['role' => $role->value]);
    }

    /**
     * Get all tasks for the team.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get pending tasks for the team.
     */
    public function pendingTasks(): HasMany
    {
        return $this->tasks()->where('status', 'pending');
    }

    /**
     * Get in-progress tasks for the team.
     */
    public function inProgressTasks(): HasMany
    {
        return $this->tasks()->where('status', 'in_progress');
    }

    /**
     * Get completed tasks for the team.
     */
    public function completedTasks(): HasMany
    {
        return $this->tasks()->where('status', 'completed');
    }

    /**
     * Get overdue tasks for the team.
     */
    public function overdueTasks(): HasMany
    {
        return $this->tasks()
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }
}

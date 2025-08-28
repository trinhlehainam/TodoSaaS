<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * @method static User|null find($id, $columns = ['*'])
 * @method static User findOrFail($id, $columns = ['*'])
 * @method static User create(array $attributes = [])
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Team, User>
     */
    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->using(TeamMember::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Team>
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    /**
     * @return HasMany<TeamInvitation>
     */
    public function teamInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class, 'invited_by');
    }

    /**
     * @return \Illuminate\Support\Collection<int, Team>
     */
    public function allTeams(): \Illuminate\Support\Collection
    {
        return $this->teams->merge($this->ownedTeams);
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->teams->contains($team) || $this->ownedTeams->contains($team);
    }

    public function ownsTeam(Team $team): bool
    {
        return $this->ownedTeams->contains($team);
    }

    public function switchTeam(Team $team): void
    {
        if (! $this->belongsToTeam($team)) {
            throw new \Exception('User does not belong to this team.');
        }

        $this->update(['current_team_id' => $team->id]);
    }

    public function personalTeam(): ?Team
    {
        return $this->ownedTeams()->where('personal_team', true)->first();
    }

    /**
     * Get the tasks assigned to the user.
     *
     * @return HasMany<Task>
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the tasks assigned to the user in a specific team.
     */
    public function tasksInTeam(Team $team): HasMany
    {
        return $this->assignedTasks()->where('team_id', $team->id);
    }
}

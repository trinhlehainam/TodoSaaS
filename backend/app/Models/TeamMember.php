<?php

namespace App\Models;

use App\Enums\TeamRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamMember extends Pivot
{
    use HasFactory;

    protected $table = 'team_members';

    public $incrementing = true;

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'role' => TeamRole::class,
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOwner(): bool
    {
        return $this->role === TeamRole::Owner;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [TeamRole::Owner, TeamRole::Admin]);
    }

    public function canManageTeam(): bool
    {
        return $this->isAdmin();
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->role->permissions()) ||
               in_array('*', $this->role->permissions());
    }
}

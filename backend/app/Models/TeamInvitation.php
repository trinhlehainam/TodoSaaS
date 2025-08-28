<?php

namespace App\Models;

use App\Enums\TeamRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeamInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'email',
        'role',
        'token',
        'invited_by',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => TeamRole::class,
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TeamInvitation $invitation) {
            if (! $invitation->token) {
                $invitation->token = Str::random(32);
            }
            if (! $invitation->expires_at) {
                $invitation->expires_at = now()->addDays(7);
            }
        });
    }

    /**
     * @return BelongsTo<Team, TeamInvitation>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<User, TeamInvitation>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function accept(User $user): void
    {
        if ($this->isExpired()) {
            throw new \Exception('This invitation has expired.');
        }

        $this->team->addMember($user, $this->role);
        $this->delete();
    }

    public function reject(): void
    {
        $this->delete();
    }
}

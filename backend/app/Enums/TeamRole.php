<?php

namespace App\Enums;

enum TeamRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Admin => 'Administrator',
            self::Member => 'Member',
            self::Viewer => 'Viewer',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::Owner => ['*'],
            self::Admin => ['manage-team', 'manage-members', 'view-team'],
            self::Member => ['view-team', 'contribute'],
            self::Viewer => ['view-team'],
        };
    }
}

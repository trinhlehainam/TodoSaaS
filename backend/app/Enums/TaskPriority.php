<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * Get the label for the priority.
     */
    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
        };
    }

    /**
     * Get the color for the priority.
     */
    public function color(): string
    {
        return match ($this) {
            self::Low => 'gray',
            self::Medium => 'yellow',
            self::High => 'red',
        };
    }

    /**
     * Get the icon for the priority.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Low => 'arrow-down',
            self::Medium => 'minus',
            self::High => 'arrow-up',
        };
    }
}

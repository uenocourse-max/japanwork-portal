<?php

namespace App\Enums\Concerns;

trait HasHexColor
{
    public function hexColor(): string
    {
        return match ($this->color()) {
            'gray' => '#9ca3af',
            'info' => '#3b82f6',
            'success' => '#22c55e',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            default => '#3b82f6',
        };
    }
}

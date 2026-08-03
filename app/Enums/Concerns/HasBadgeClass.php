<?php

namespace App\Enums\Concerns;

trait HasBadgeClass
{
    public function badgeClass(): string
    {
        return match ($this->color()) {
            'gray' => 'bg-gray-100 text-gray-600',
            'info' => 'bg-blue-100 text-blue-800',
            'success' => 'bg-green-100 text-green-800',
            'warning' => 'bg-amber-100 text-amber-800',
            'danger' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}

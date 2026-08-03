<?php

namespace App\Enums;

use App\Enums\Concerns\HasBadgeClass;

enum JlptLevel: string
{
    use HasBadgeClass;

    case N1 = 'N1';
    case N2 = 'N2';
    case N3 = 'N3';
    case N4 = 'N4';
    case N5 = 'N5';
    case JftBasicA2 = 'JFT Basic A2';

    public function label(): string
    {
        return $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::N1 => 'danger',
            self::N2 => 'warning',
            self::N3 => 'info',
            self::N4 => 'success',
            self::N5 => 'gray',
            self::JftBasicA2 => 'info',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $level): array => [$level->value => $level->value])
            ->all();
    }
}

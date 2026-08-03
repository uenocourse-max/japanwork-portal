<?php

namespace App\Enums;

enum JobType: string
{
    case Magang = 'magang';
    case TokuteiGinou = 'tg';
    case Engineer = 'engineer';

    public function label(): string
    {
        return match ($this) {
            self::Magang => 'Magang',
            self::TokuteiGinou => 'Tokutei Ginou (SSW)',
            self::Engineer => 'Engineer / Gijinkoku',
        };
    }

    public function tableLabel(): string
    {
        return match ($this) {
            self::Magang => 'Magang',
            self::TokuteiGinou => 'TG (SSW)',
            self::Engineer => 'Engineer / Gijinkoku',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Magang => 'info',
            self::TokuteiGinou => 'warning',
            self::Engineer => 'success',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])
            ->all();
    }
}

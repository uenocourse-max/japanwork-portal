<?php

namespace App\Enums;

use App\Enums\Concerns\HasBadgeClass;
use App\Enums\Concerns\HasHexColor;

enum MatchingStatus: string
{
    use HasBadgeClass, HasHexColor;

    case NotMatched = 'not_matched';
    case ProcessMatching = 'process_matching';
    case WaitingResult = 'waiting_result';
    case Matched = 'matched';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::NotMatched => 'Belum Matched',
            self::ProcessMatching => 'Proses Matching',
            self::WaitingResult => 'Menunggu Hasil',
            self::Matched => 'Matched',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NotMatched => 'gray',
            self::ProcessMatching => 'warning',
            self::WaitingResult => 'info',
            self::Matched => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }
}

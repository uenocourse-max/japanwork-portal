<?php

namespace App\Enums;

use App\Enums\Concerns\HasBadgeClass;
use App\Enums\Concerns\HasHexColor;

enum ApplicationStatus: string
{
    use HasBadgeClass, HasHexColor;

    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case Accepted = 'accepted';
    case InterviewScheduled = 'interview_scheduled';
    case CompanyAccepted = 'company_accepted';
    case NotPassed = 'not_passed';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Reviewed => 'Direview',
            self::Accepted => 'Diterima',
            self::InterviewScheduled => 'Interview Terjadwal',
            self::CompanyAccepted => 'Diterima Perusahaan',
            self::NotPassed => 'Tidak Lolos',
            self::Rejected => 'Ditolak',
            self::Withdrawn => 'Ditarik',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Reviewed => 'info',
            self::Accepted => 'success',
            self::InterviewScheduled => 'warning',
            self::CompanyAccepted => 'success',
            self::NotPassed => 'danger',
            self::Rejected => 'danger',
            self::Withdrawn => 'gray',
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

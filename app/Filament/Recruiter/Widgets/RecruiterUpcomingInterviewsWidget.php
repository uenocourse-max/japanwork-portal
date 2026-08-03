<?php

namespace App\Filament\Recruiter\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as Widget;

class RecruiterUpcomingInterviewsWidget extends Widget
{
    protected ?string $pollingInterval = null;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JobApplication::whereHas('jobListing', fn ($q) => $q->where('posted_by', auth()->id()))
                    ->where('status', ApplicationStatus::InterviewScheduled->value)
                    ->where('interview_date', '>=', Carbon::now()->startOfDay())
                    ->with('student', 'jobListing')
                    ->orderBy('interview_date')
            )
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->limit(25)
                    ->weight('bold'),
                TextColumn::make('jobListing.title')
                    ->label('Lowongan')
                    ->limit(25),
                TextColumn::make('interview_date')
                    ->label('Tanggal Interview')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('interview_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'online' => 'Online',
                        'offline' => 'Offline',
                        'telepon' => 'Telepon',
                        default => ucfirst($state ?? '-'),
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'online' => 'info',
                        'offline' => 'success',
                        'telepon' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('interview_location')
                    ->label('Lokasi')
                    ->limit(20)
                    ->placeholder('-'),
            ]);
    }
}

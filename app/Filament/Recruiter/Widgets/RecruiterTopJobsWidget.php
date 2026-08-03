<?php

namespace App\Filament\Recruiter\Widgets;

use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Filament\Recruiter\Resources\RecruiterJobListingResource;
use App\Models\JobListing;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as Widget;

class RecruiterTopJobsWidget extends Widget
{
    protected ?string $pollingInterval = null;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JobListing::where('posted_by', auth()->id())
                    ->withCount('applications')
                    ->orderByDesc('applications_count')
            )
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Lowongan')
                    ->limit(30)
                    ->weight('bold'),
                TextColumn::make('job_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => JobType::tryFrom($state)?->tableLabel() ?? ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => JobType::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->limit(15),
                TextColumn::make('applications_count')
                    ->label('Lamaran')
                    ->counts('applications')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => JobStatus::tryFrom($state)?->label() ?? ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => JobStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('deadline')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (JobListing $record): string => RecruiterJobListingResource::getUrl('edit', ['record' => $record->id])),
            ]);
    }
}

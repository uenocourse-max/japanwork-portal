<?php

namespace App\Filament\Recruiter\Resources\RecruiterJobListings\Tables;

use App\Enums\JlptLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class RecruiterJobListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_display_url')
                    ->label('Thumbnail')
                    ->circular()
                    ->size(40)
                    ->placeholder(fn ($record): string => 'https://ui-avatars.com/api/?name='.urlencode(substr($record->company_name, 0, 1)).'&background=6366f1&color=fff&bold=1&size=40'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sswCategory.name')
                    ->label('SSW')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('job_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => JobType::tryFrom($state)?->tableLabel() ?? $state)
                    ->color(fn (string $state): string => JobType::tryFrom($state)?->color() ?? 'gray'),
                Tables\Columns\TextColumn::make('jlpt_level_required')
                    ->label('JLPT')
                    ->badge()
                    ->color(fn (?string $state): string => $state
                        ? (JlptLevel::tryFrom($state)?->color() ?? 'gray')
                        : 'gray')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Lamaran')
                    ->counts('applications')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => JobStatus::tryFrom($state ?? '')?->label() ?? $state ?? '-')
                    ->color(fn (?string $state): string => JobStatus::tryFrom($state ?? '')?->color() ?? 'gray'),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(JobStatus::options()),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => $record->status !== JobStatus::Filled->value),
                Action::make('close')
                    ->label('Tutup Lowongan')
                    ->color('danger')
                    ->icon('heroicon-o-lock-closed')
                    ->visible(fn ($record) => $record->status === JobStatus::Open->value)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => JobStatus::Closed->value]);
                        Notification::make()->title('Lowongan ditutup')->success()->send();
                    }),
                Action::make('mark_filled')
                    ->label('Tandai Terisi')
                    ->color('info')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn ($record) => $record->status === JobStatus::Open->value)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => JobStatus::Filled->value]);
                        Notification::make()->title('Lowongan ditandai terisi')->success()->send();
                    }),
                Action::make('reopen')
                    ->label('Buka Kembali')
                    ->color('success')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn ($record) => in_array($record->status, [
                        JobStatus::Closed->value,
                        JobStatus::Filled->value,
                    ]))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => JobStatus::Open->value]);
                        Notification::make()->title('Lowongan dibuka kembali')->success()->send();
                    }),
            ]);
    }
}

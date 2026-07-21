<?php

namespace App\Filament\Resources\JobListings\Tables;

use App\Models\LpkTsk;
use App\Models\SswCategory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class JobListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_display_url')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->circular()
                    ->size(40)
                    ->placeholder(fn ($record): string => 'https://ui-avatars.com/api/?name='.urlencode(substr($record->company_name, 0, 1)).'&background=6366f1&color=fff&bold=1&size=40')
                    ->default(['alignCenter' => false]),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sswCategory.name')
                    ->label('SSW')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('job_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'magang' => 'Magang',
                        'tg' => 'TG (SSW)',
                        'engineer' => 'Engineer / Gijinkoku',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'magang' => 'info',
                        'tg' => 'warning',
                        'engineer' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('jlpt_level_required')
                    ->label('JLPT')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'N1' => 'danger',
                        'N2' => 'warning',
                        'N3' => 'info',
                        'N4' => 'success',
                        'N5' => 'gray',
                        'JFT Basic A2' => 'info',
                        default => 'gray',
                    })
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('salary_min')
                    ->label('Gaji Min')
                    ->money('JPY', 0)
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('salary_max')
                    ->label('Gaji Max')
                    ->money('JPY', 0)
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Lamaran')
                    ->counts('applications')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'draft' => 'gray',
                        'closed' => 'danger',
                        'filled' => 'info',
                    }),
                Tables\Columns\TextColumn::make('poster.lpkTsk.name')
                    ->label('LPK/TSK')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
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
                    ->options([
                        'open' => 'Dibuka',
                        'draft' => 'Draft',
                        'closed' => 'Ditutup',
                        'filled' => 'Terisi',
                    ]),
                Tables\Filters\SelectFilter::make('ssw_category_id')
                    ->label('SSW')
                    ->options(SswCategory::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('jlpt_level_required')
                    ->label('JLPT')
                    ->options(['N5' => 'N5', 'N4' => 'N4', 'N3' => 'N3', 'N2' => 'N2', 'N1' => 'N1', 'JFT Basic A2' => 'JFT Basic A2']),
                Tables\Filters\SelectFilter::make('posted_by')
                    ->label('LPK/TSK')
                    ->options(fn () => LpkTsk::pluck('name', 'user_id'))
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => $record->status !== 'filled'),
                Action::make('close')
                    ->label('Tutup Lowongan')
                    ->color('danger')
                    ->icon('heroicon-o-lock-closed')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'closed']);
                        Notification::make()->title('Lowongan ditutup')->success()->send();
                    }),
                Action::make('mark_filled')
                    ->label('Tandai Terisi')
                    ->color('info')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'filled']);
                        Notification::make()->title('Lowongan ditandai terisi')->success()->send();
                    }),
                Action::make('reopen')
                    ->label('Buka Kembali')
                    ->color('success')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn ($record) => in_array($record->status, ['closed', 'filled']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'open']);
                        Notification::make()->title('Lowongan dibuka kembali')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

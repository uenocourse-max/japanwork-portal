<?php

namespace App\Filament\Resources\JobListings\RelationManagers;

use App\Filament\Recruiter\Resources\RecruiterApplications\Tables\RecruiterApplicationsTable;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.jlpt_level')
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
                TextColumn::make('student.phone_number')
                    ->label('Telepon')
                    ->searchable(),
                TextColumn::make('student.matching_status')
                    ->label('Status Matching')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'matched' => 'success',
                        'process_matching' => 'warning',
                        'waiting_result' => 'info',
                        'not_matched' => 'gray',
                        'cancelled' => 'danger',
                    }),
                TextColumn::make('status')
                    ->label('Status Lamaran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => RecruiterApplicationsTable::getStatusLabel($state))
                    ->color(fn (string $state): string => RecruiterApplicationsTable::getStatusColor($state)),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->placeholder('-'),
                TextColumn::make('applied_at')
                    ->label('Tanggal Lamar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('applied_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'reviewed' => 'Sudah Direview',
                        'accepted' => 'Diterima',
                        'interview_scheduled' => 'Jadwal Interview',
                        'company_accepted' => 'Diterima Perusahaan',
                        'not_passed' => 'Tidak Lolos',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Review Lamaran')
                    ->modalDescription('Tandai lamaran ini sudah direview.')
                    ->visible(fn ($record): bool => $record->status === 'pending')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tambahkan catatan review...')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldStatus = $record->status;
                        $record->update([
                            'status' => 'reviewed',
                            'notes' => $data['notes'] ?: $record->notes,
                            'reviewed_at' => now(),
                        ]);
                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
                    }),
                Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Lamaran')
                    ->modalDescription('Lamaran ini akan diproses ke tahap interview.')
                    ->visible(fn ($record): bool => $record->status === 'reviewed')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan penerimaan...')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldStatus = $record->status;
                        $record->update([
                            'status' => 'accepted',
                            'notes' => $data['notes'] ?: $record->notes,
                            'reviewed_at' => $record->reviewed_at ?? now(),
                        ]);
                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
                    }),
                Action::make('schedule_interview')
                    ->label('Jadwalkan Interview')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->modalHeading('Jadwalkan Interview')
                    ->modalDescription('Isi detail jadwal interview.')
                    ->visible(fn ($record): bool => $record->status === 'accepted')
                    ->form([
                        Select::make('interview_type')
                            ->label('Jenis Interview')
                            ->options(['online' => 'Online (Meeting)', 'offline' => 'Langsung'])
                            ->required(),
                        DateTimePicker::make('interview_date')
                            ->label('Tanggal & Waktu')
                            ->required()
                            ->native(false)
                            ->minutesStep(15),
                        TextInput::make('interview_location')
                            ->label('Lokasi / Link Meeting')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('interview_notes')
                            ->label('Catatan')
                            ->rows(2),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldStatus = $record->status;
                        $record->update([
                            'status' => 'interview_scheduled',
                            'interview_type' => $data['interview_type'],
                            'interview_date' => $data['interview_date'],
                            'interview_location' => $data['interview_location'],
                            'interview_notes' => $data['interview_notes'],
                        ]);
                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
                    }),
                Action::make('input_result')
                    ->label('Input Hasil')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->modalHeading('Input Hasil Interview')
                    ->modalDescription('Pilih hasil akhir interview.')
                    ->visible(fn ($record): bool => $record->status === 'interview_scheduled')
                    ->form([
                        Select::make('result')
                            ->label('Hasil')
                            ->options([
                                'company_accepted' => 'Diterima Perusahaan',
                                'not_passed' => 'Tidak Lolos',
                            ])
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldStatus = $record->status;
                        $newStatus = $data['result'];
                        $record->update([
                            'status' => $newStatus,
                            'notes' => $data['notes'] ?: $record->notes,
                        ]);

                        if ($newStatus === 'company_accepted') {
                            $student = $record->student;
                            if ($student) {
                                $student->update([
                                    'matching_status' => 'matched',
                                    'matched_company_name' => $record->jobListing->company_name,
                                ]);
                            }
                        }

                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Lamaran')
                    ->modalDescription('Lamaran siswa ini akan ditolak.')
                    ->visible(fn ($record): bool => ! in_array($record->status, ['company_accepted', 'not_passed', 'rejected']))
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Alasan penolakan...')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldStatus = $record->status;
                        $record->update([
                            'status' => 'rejected',
                            'notes' => $data['notes'] ?: $record->notes,
                            'reviewed_at' => $record->reviewed_at ?? now(),
                        ]);
                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

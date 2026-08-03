<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Enums\ApplicationStatus;
use App\Enums\JlptLevel;
use App\Enums\MatchingStatus;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusChanged;
use App\Notifications\InterviewScheduleChanged;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jobListing.title')
                    ->label('Lowongan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jobListing.company_name')
                    ->label('Perusahaan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.jlpt_level')
                    ->label('JLPT')
                    ->badge()
                    ->color(fn (?string $state): string => $state
                        ? (JlptLevel::tryFrom($state)?->color() ?? 'gray')
                        : 'gray')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('student.phone_number')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ApplicationStatus::tryFrom($state)?->label() ?? ucfirst($state))
                    ->color(fn (string $state): string => ApplicationStatus::tryFrom($state)?->color() ?? 'gray'),
                Tables\Columns\TextColumn::make('applied_at')
                    ->label('Tanggal Lamar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Direview')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->defaultSort('applied_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(ApplicationStatus::options()),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Review Lamaran')
                    ->modalDescription('Tandai lamaran ini sudah direview.')
                    ->visible(fn ($record): bool => $record->status === ApplicationStatus::Pending->value)
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tambahkan catatan review...')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldStatus = $record->status;
                        $record->update([
                            'status' => ApplicationStatus::Reviewed->value,
                            'notes' => $data['notes'] ?: $record->notes,
                            'reviewed_at' => now(),
                        ]);
                        self::notifyStatusChange($record->fresh(), $oldStatus);
                    }),

                Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Lamaran')
                    ->modalDescription('Lamaran ini akan diproses ke tahap interview.')
                    ->visible(fn ($record): bool => $record->status === ApplicationStatus::Reviewed->value)
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan penerimaan...')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldStatus = $record->status;
                        $record->update([
                            'status' => ApplicationStatus::Accepted->value,
                            'notes' => $data['notes'] ?: $record->notes,
                            'reviewed_at' => $record->reviewed_at ?? now(),
                        ]);
                        self::notifyStatusChange($record->fresh(), $oldStatus);
                    }),

                Action::make('schedule_interview')
                    ->label('Jadwalkan Interview')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->modalHeading('Jadwalkan Interview')
                    ->modalDescription('Isi detail jadwal interview.')
                    ->visible(fn ($record): bool => $record->status === ApplicationStatus::Accepted->value)
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
                            'status' => ApplicationStatus::InterviewScheduled->value,
                            'interview_type' => $data['interview_type'],
                            'interview_date' => $data['interview_date'],
                            'interview_location' => $data['interview_location'],
                            'interview_notes' => $data['interview_notes'],
                        ]);
                        self::notifyStatusChange($record->fresh(), $oldStatus);
                    }),

                Action::make('edit_interview')
                    ->label('Edit Jadwal')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->modalHeading('Edit Jadwal Interview')
                    ->modalDescription('Ubah detail jadwal interview yang sudah dijadwalkan.')
                    ->visible(fn ($record): bool => $record->status === ApplicationStatus::InterviewScheduled->value)
                    ->form(fn (JobApplication $record): array => [
                        Select::make('interview_type')
                            ->label('Jenis Interview')
                            ->options(['online' => 'Online (Meeting)', 'offline' => 'Langsung'])
                            ->required()
                            ->default($record->interview_type),
                        DateTimePicker::make('interview_date')
                            ->label('Tanggal & Waktu')
                            ->required()
                            ->native(false)
                            ->minutesStep(15)
                            ->default($record->interview_date),
                        TextInput::make('interview_location')
                            ->label('Lokasi / Link Meeting')
                            ->required()
                            ->maxLength(255)
                            ->default($record->interview_location),
                        Textarea::make('interview_notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->default($record->interview_notes),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldSchedule = [
                            'interview_type' => $record->interview_type,
                            'interview_date' => $record->interview_date?->format('d M Y H:i'),
                            'interview_location' => $record->interview_location,
                            'interview_notes' => $record->interview_notes,
                        ];

                        $record->update([
                            'interview_type' => $data['interview_type'],
                            'interview_date' => $data['interview_date'],
                            'interview_location' => $data['interview_location'],
                            'interview_notes' => $data['interview_notes'],
                        ]);

                        $newSchedule = [
                            'interview_type' => $data['interview_type'],
                            'interview_date' => Carbon::parse($data['interview_date'])->format('d M Y H:i'),
                            'interview_location' => $data['interview_location'],
                            'interview_notes' => $data['interview_notes'],
                        ];

                        $studentUser = $record->fresh()->student?->user;
                        if ($studentUser) {
                            $studentUser->notify(new InterviewScheduleChanged($record->fresh(), $oldSchedule, $newSchedule));
                        }
                    }),

                Action::make('input_result')
                    ->label('Input Hasil')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->modalHeading('Input Hasil Interview')
                    ->modalDescription('Pilih hasil akhir interview.')
                    ->visible(fn ($record): bool => $record->status === ApplicationStatus::InterviewScheduled->value)
                    ->form([
                        Select::make('result')
                            ->label('Hasil')
                            ->options([
                                ApplicationStatus::CompanyAccepted->value => 'Diterima Perusahaan',
                                ApplicationStatus::NotPassed->value => 'Tidak Lolos',
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

                        if ($newStatus === ApplicationStatus::CompanyAccepted->value) {
                            $student = $record->student;
                            if ($student) {
                                $student->update([
                                    'matching_status' => MatchingStatus::Matched->value,
                                    'matched_company_name' => $record->jobListing->company_name,
                                ]);
                            }
                        }

                        self::notifyStatusChange($record->fresh(), $oldStatus);
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Lamaran')
                    ->modalDescription('Lamaran siswa ini akan ditolak.')
                    ->visible(fn ($record): bool => ! in_array($record->status, [
                        ApplicationStatus::CompanyAccepted->value,
                        ApplicationStatus::NotPassed->value,
                        ApplicationStatus::Rejected->value,
                    ]))
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Alasan penolakan...')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldStatus = $record->status;
                        $record->update([
                            'status' => ApplicationStatus::Rejected->value,
                            'notes' => $data['notes'] ?: $record->notes,
                            'reviewed_at' => $record->reviewed_at ?? now(),
                        ]);
                        self::notifyStatusChange($record->fresh(), $oldStatus);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function notifyStatusChange(JobApplication $record, string $oldStatus): void
    {
        $studentUser = $record->student?->user;
        if ($studentUser) {
            $studentUser->notify(new ApplicationStatusChanged($record, $oldStatus));
        }
    }
}

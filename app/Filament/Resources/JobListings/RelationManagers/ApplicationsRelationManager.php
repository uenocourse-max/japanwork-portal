<?php

namespace App\Filament\Resources\JobListings\RelationManagers;

use App\Enums\ApplicationStatus;
use App\Enums\JlptLevel;
use App\Enums\MatchingStatus;
use App\Filament\Recruiter\Resources\RecruiterApplications\Tables\RecruiterApplicationsTable;
use App\Models\JobApplication;
use App\Notifications\InterviewScheduleChanged;
use Carbon\Carbon;
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
                    ->color(fn (?string $state): string => $state
                        ? (JlptLevel::tryFrom($state)?->color() ?? 'gray')
                        : 'gray')
                    ->placeholder('-'),
                TextColumn::make('student.phone_number')
                    ->label('Telepon')
                    ->searchable(),
                TextColumn::make('student.matching_status')
                    ->label('Status Matching')
                    ->badge()
                    ->color(fn (?string $state): string => $state
                        ? (MatchingStatus::tryFrom($state)?->color() ?? 'gray')
                        : 'gray'),
                TextColumn::make('status')
                    ->label('Status Lamaran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ApplicationStatus::tryFrom($state)?->label() ?? ucfirst($state))
                    ->color(fn (string $state): string => ApplicationStatus::tryFrom($state)?->color() ?? 'gray'),
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
                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
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
                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
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
                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
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

                        RecruiterApplicationsTable::notifyStatusChange($record->fresh(), $oldStatus);
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

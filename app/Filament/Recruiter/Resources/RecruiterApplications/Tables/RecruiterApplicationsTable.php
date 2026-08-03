<?php

namespace App\Filament\Recruiter\Resources\RecruiterApplications\Tables;

use App\Enums\ApplicationStatus;
use App\Enums\JlptLevel;
use App\Enums\MatchingStatus;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusChanged;
use App\Notifications\InterviewScheduleChanged;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;

class RecruiterApplicationsTable
{
    public static function notifyStatusChange(JobApplication $record, string $oldStatus): void
    {
        $studentUser = $record->student?->user;
        if ($studentUser) {
            $studentUser->notify(new ApplicationStatusChanged($record, $oldStatus));
        }
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jobListing.title')
                    ->label('Lowongan')
                    ->searchable()
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
            ])
            ->defaultSort('applied_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(ApplicationStatus::options()),
            ])
            ->recordActions([
                Action::make('view_profile')
                    ->label('Lihat Profil')
                    ->icon('heroicon-o-user')
                    ->color('primary')
                    ->modalHeading(fn (JobApplication $record): string => 'Profil: '.$record->student?->full_name)
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->extraModalFooterActions(function (JobApplication $record): array {
                        $actions = [];

                        if ($record->status === ApplicationStatus::Accepted->value) {
                            $actions[] = Action::make('schedule_from_profile')
                                ->label('Jadwalkan Interview')
                                ->icon('heroicon-o-calendar')
                                ->color('warning')
                                ->requiresConfirmation()
                                ->modalHeading('Jadwalkan Interview')
                                ->modalDescription('Isi detail jadwal interview.')
                                ->schema([
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
                                ->action(function (array $data, JobApplication $record): void {
                                    $oldStatus = $record->status;
                                    $record->update([
                                        'status' => ApplicationStatus::InterviewScheduled->value,
                                        'interview_type' => $data['interview_type'],
                                        'interview_date' => $data['interview_date'],
                                        'interview_location' => $data['interview_location'],
                                        'interview_notes' => $data['interview_notes'],
                                    ]);
                                    self::notifyStatusChange($record->fresh(), $oldStatus);
                                });
                        }

                        if ($record->status === ApplicationStatus::InterviewScheduled->value) {
                            $actions[] = Action::make('edit_from_profile')
                                ->label('Edit Jadwal Interview')
                                ->icon('heroicon-o-pencil-square')
                                ->color('primary')
                                ->modalHeading('Edit Jadwal Interview')
                                ->modalDescription('Ubah detail jadwal interview yang sudah dijadwalkan.')
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
                                ->action(function (array $data, JobApplication $record): void {
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
                                });

                            $actions[] = Action::make('company_accept_from_profile')
                                ->label('Terima Perusahaan')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->requiresConfirmation()
                                ->modalHeading('Diterima Perusahaan')
                                ->modalDescription('Lamaran siswa ini dinyatakan lolos dan diterima oleh perusahaan.')
                                ->schema([
                                    Textarea::make('notes')
                                        ->label('Catatan')
                                        ->rows(3),
                                ])
                                ->action(function (array $data, JobApplication $record): void {
                                    $oldStatus = $record->status;
                                    $record->update([
                                        'status' => ApplicationStatus::CompanyAccepted->value,
                                        'notes' => $data['notes'] ?: $record->notes,
                                    ]);

                                    $student = $record->student;
                                    if ($student) {
                                        $student->update([
                                            'matching_status' => MatchingStatus::Matched->value,
                                            'matched_company_name' => $record->jobListing->company_name,
                                        ]);
                                    }

                                    self::notifyStatusChange($record->fresh(), $oldStatus);
                                });

                            $actions[] = Action::make('not_passed_from_profile')
                                ->label('Tidak Lolos')
                                ->icon('heroicon-o-x-circle')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->modalHeading('Tidak Lolos')
                                ->modalDescription('Lamaran siswa ini dinyatakan tidak lolos.')
                                ->schema([
                                    Textarea::make('notes')
                                        ->label('Catatan')
                                        ->rows(3),
                                ])
                                ->action(function (array $data, JobApplication $record): void {
                                    $oldStatus = $record->status;
                                    $record->update([
                                        'status' => ApplicationStatus::NotPassed->value,
                                        'notes' => $data['notes'] ?: $record->notes,
                                    ]);
                                    self::notifyStatusChange($record->fresh(), $oldStatus);
                                });
                        }

                        if (! in_array($record->status, [
                            ApplicationStatus::CompanyAccepted->value,
                            ApplicationStatus::NotPassed->value,
                            ApplicationStatus::Rejected->value,
                        ])) {
                            $actions[] = Action::make('reject_from_profile')
                                ->label('Tolak')
                                ->icon('heroicon-o-x-circle')
                                ->color('gray')
                                ->requiresConfirmation()
                                ->modalHeading('Tolak Lamaran')
                                ->modalDescription('Lamaran siswa ini akan ditolak.')
                                ->schema([
                                    Textarea::make('notes')
                                        ->label('Catatan')
                                        ->rows(3),
                                ])
                                ->action(function (array $data, JobApplication $record): void {
                                    $oldStatus = $record->status;
                                    $record->update([
                                        'status' => ApplicationStatus::Rejected->value,
                                        'notes' => $data['notes'] ?: $record->notes,
                                        'reviewed_at' => $record->reviewed_at ?? now(),
                                    ]);
                                    self::notifyStatusChange($record->fresh(), $oldStatus);
                                });
                        }

                        return $actions;
                    })
                    ->schema(function (JobApplication $record): array {
                        $student = $record->student->load('sswCategories');

                        return [
                            Section::make('Data Pribadi')
                                ->schema([
                                    Grid::make(3)->schema([
                                        Placeholder::make('full_name')
                                            ->label('Nama Lengkap')
                                            ->content($student->full_name),
                                        Placeholder::make('age')
                                            ->label('Umur')
                                            ->content($student->age.' tahun'),
                                        Placeholder::make('gender')
                                            ->label('Jenis Kelamin')
                                            ->content($student->gender === 'male' ? 'Laki-laki' : 'Perempuan'),
                                        Placeholder::make('birth_place')
                                            ->label('Tempat Lahir')
                                            ->content($student->birth_place),
                                        Placeholder::make('birth_date')
                                            ->label('Tanggal Lahir')
                                            ->content($student->birth_date?->format('d M Y')),
                                        Placeholder::make('blood_type')
                                            ->label('Gol. Darah')
                                            ->content($student->blood_type),
                                        Placeholder::make('height_cm')
                                            ->label('Tinggi')
                                            ->content($student->height_cm.' cm'),
                                        Placeholder::make('weight_kg')
                                            ->label('Berat')
                                            ->content($student->weight_kg.' kg'),
                                        Placeholder::make('marital_status')
                                            ->label('Status')
                                            ->content($student->marital_status === 'single' ? 'Single' : 'Married'),
                                    ]),
                                ])->compact(),

                            Section::make('Kontak')
                                ->schema([
                                    Grid::make(2)->schema([
                                        Placeholder::make('phone_number')
                                            ->label('Telepon')
                                            ->content($student->phone_number),
                                        Placeholder::make('address')
                                            ->label('Alamat')
                                            ->content($student->address),
                                    ]),
                                ])->compact(),

                            Section::make('Bahasa Jepang')
                                ->schema([
                                    Grid::make(3)->schema([
                                        Placeholder::make('jlpt_level')
                                            ->label('Level JLPT')
                                            ->content($student->jlpt_level ?? 'Tidak ada'),
                                        Placeholder::make('jft_score')
                                            ->label('Skor JFT')
                                            ->content($student->jft_score ? (string) $student->jft_score : 'Tidak ada'),
                                        Placeholder::make('japanese_learning_months')
                                            ->label('Lama Belajar')
                                            ->content($student->japanese_learning_months.' bulan'),
                                    ]),
                                ])->compact(),

                            Section::make('Program & Matching')
                                ->schema([
                                    Grid::make(3)->schema([
                                        Placeholder::make('participant_status')
                                            ->label('Status Peserta')
                                            ->content($student->participant_status === 'ex' ? 'Eks' : 'New Comer'),
                                        Placeholder::make('pathway')
                                            ->label('Jalur')
                                            ->content($student->pathway === 'lpk' ? 'LPK' : 'Mandiri'),
                                        Placeholder::make('lpk_name')
                                            ->label('LPK')
                                            ->content($student->lpk_name ?? '-'),
                                        Placeholder::make('matching_status')
                                            ->label('Status Matching')
                                            ->content(MatchingStatus::tryFrom($student->matching_status)?->label() ?? $student->matching_status),
                                        Placeholder::make('matched_company_name')
                                            ->label('Perusahaan Match')
                                            ->content($student->matched_company_name ?? '-'),
                                    ]),
                                ])->compact(),

                            Section::make('Sertifikat SSW')
                                ->schema([
                                    Placeholder::make('ssw_categories')
                                        ->label('Kategori SSW')
                                        ->content($student->sswCategories->pluck('name')->join(', ') ?: 'Belum ada'),
                                ])->compact(),

                            Section::make('Dokumen')
                                ->schema([
                                    Grid::make(2)->schema([
                                        Placeholder::make('photo')
                                            ->label('Foto')
                                            ->content($student->photo_drive_url
                                                ? '<a href="'.$student->photo_drive_url.'" target="_blank" class="text-blue-600 hover:underline">Lihat Foto</a>'
                                                : 'Tidak ada')
                                            ->html(),
                                        Placeholder::make('cv')
                                            ->label('CV')
                                            ->content($student->cv_drive_url
                                                ? '<a href="'.$student->cv_drive_url.'" target="_blank" class="text-blue-600 hover:underline">Lihat CV</a>'
                                                : 'Tidak ada')
                                            ->html(),
                                    ]),
                                ])->compact(),

                            Section::make('Lowongan yang Dilamar')
                                ->schema([
                                    Grid::make(2)->schema([
                                        Placeholder::make('job_title')
                                            ->label('Judul Lowongan')
                                            ->content($record->jobListing->title),
                                        Placeholder::make('applied_at')
                                            ->label('Tanggal Lamar')
                                            ->content($record->applied_at?->format('d M Y H:i')),
                                        Placeholder::make('application_status')
                                            ->label('Status Lamaran')
                                            ->content(ApplicationStatus::tryFrom($record->status)?->label() ?? $record->status),
                                        Placeholder::make('notes')
                                            ->label('Catatan')
                                            ->content($record->notes ?? '-'),
                                    ]),
                                ])->compact(),
                        ];
                    }),

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
                    ->modalDescription('Lamaran ini akan diproses ke tahap interview. Status matching belum diperbarui.')
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
                    ->modalDescription('Isi detail jadwal interview untuk siswa ini.')
                    ->visible(fn ($record): bool => $record->status === ApplicationStatus::Accepted->value)
                    ->schema([
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
                    ->schema([
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
                        $updateData = [
                            'status' => $newStatus,
                            'notes' => $data['notes'] ?: $record->notes,
                        ];

                        $record->update($updateData);

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
                    ->color('gray')
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
            ]);
    }
}

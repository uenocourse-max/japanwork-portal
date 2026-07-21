<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\SswCategory;
use App\Models\Student;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Siswa';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Siswa';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Akun')
                    ->schema([
                        Forms\Components\TextInput::make('user.email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Nama Pengguna')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('user.password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),
                    ])->columns(3),

                Section::make('Data Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('age')
                            ->label('Umur')
                            ->numeric()
                            ->required()
                            ->minValue(15)
                            ->maxValue(60),
                        Forms\Components\TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->required(),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->required()
                            ->rows(2),
                        Forms\Components\TextInput::make('height_cm')
                            ->label('Tinggi Badan (cm)')
                            ->numeric()
                            ->required()
                            ->minValue(100)
                            ->maxValue(250),
                        Forms\Components\TextInput::make('weight_kg')
                            ->label('Berat Badan (kg)')
                            ->numeric()
                            ->required()
                            ->minValue(30)
                            ->maxValue(200),
                        Forms\Components\Select::make('blood_type')
                            ->label('Golongan Darah')
                            ->options(['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O'])
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['male' => 'Laki-laki', 'female' => 'Perempuan'])
                            ->required(),
                        Forms\Components\Select::make('marital_status')
                            ->label('Status Perkawinan')
                            ->options(['single' => 'Single', 'married' => 'Married'])
                            ->required(),
                        Forms\Components\TextInput::make('phone_number')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ])->columns(3),

                Section::make('Kemampuan Bahasa Jepang')
                    ->schema([
                        Forms\Components\TextInput::make('jft_score')
                            ->label('Score')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->maxValue(480),
                        Forms\Components\Select::make('jlpt_level')
                            ->label('Level JLPT')
                            ->options(['N5' => 'N5', 'N4' => 'N4', 'N3' => 'N3', 'N2' => 'N2', 'N1' => 'N1', 'JFT Basic A2' => 'JFT Basic A2'])
                            ->nullable(),
                        Forms\Components\TextInput::make('japanese_learning_months')
                            ->label('Lama Belajar Bahasa Jepang (bulan)')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                    ])->columns(3),

                Section::make('Informasi Program')
                    ->schema([
                        Forms\Components\Select::make('participant_status')
                            ->label('Status Peserta')
                            ->options(['ex' => 'Eks', 'new_comer' => 'New Comer'])
                            ->required(),
                        Forms\Components\Select::make('pathway')
                            ->label('Jalur')
                            ->options(['mandiri' => 'Mandiri', 'lpk' => 'LPK'])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('lpk_name')
                            ->label('Nama LPK')
                            ->maxLength(255)
                            ->requiredIf('pathway', 'lpk')
                            ->visible(fn (Get $get) => $get('pathway') === 'lpk'),
                        Forms\Components\Select::make('ssw_categories')
                            ->label('Kategori SSW')
                            ->options(SswCategory::pluck('name', 'id'))
                            ->multiple()
                            ->required()
                            ->preload(),
                    ])->columns(3),

                Section::make('Status Matching')
                    ->schema([
                        Forms\Components\Select::make('matching_status')
                            ->label('Status Matching')
                            ->options([
                                'not_matched' => 'Belum Matched',
                                'process_matching' => 'Proses Matching',
                                'waiting_result' => 'Menunggu Hasil',
                                'matched' => 'Matched',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('not_matched')
                            ->live(),
                        Forms\Components\TextInput::make('matched_company_name')
                            ->label('Nama Perusahaan')
                            ->maxLength(255)
                            ->nullable()
                            ->visible(fn (Get $get) => $get('matching_status') === 'matched'),
                    ])->columns(2),

                Section::make('Dokumen')
                    ->schema([
                        Forms\Components\TextInput::make('photo_drive_url')
                            ->label('Link Foto (Google Drive)')
                            ->url()
                            ->nullable(),
                        Forms\Components\TextInput::make('cv_drive_url')
                            ->label('Link CV (Google Drive)')
                            ->url()
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (?string $state): string => $state === 'male' ? 'Laki-laki' : 'Perempuan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Telepon')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jlpt_level')
                    ->label('JLPT')
                    ->sortable()
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'N1' => 'danger',
                        'N2' => 'warning',
                        'N3' => 'info',
                        'N4' => 'success',
                        'N5' => 'gray',
                        'JFT Basic A2' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sswCategories.name')
                    ->label('SSW')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->limitList(2)
                    ->expandableLimitedList(),
                Tables\Columns\TextColumn::make('pathway')
                    ->label('Jalur')
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): string => $state === 'mandiri' ? 'Mandiri' : 'LPK'),
                Tables\Columns\TextColumn::make('lpk_name')
                    ->label('LPK Asal')
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('matching_status')
                    ->label('Matching')
                    ->sortable()
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'matched' => 'success',
                        'process_matching' => 'warning',
                        'waiting_result' => 'info',
                        'not_matched' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => str_replace('_', ' ', ucfirst($state ?? ''))),
                Tables\Columns\TextColumn::make('matched_company_name')
                    ->label('Perusahaan')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options(['male' => 'Laki-laki', 'female' => 'Perempuan']),
                Tables\Filters\SelectFilter::make('jlpt_level')
                    ->label('JLPT')
                    ->options(['N5' => 'N5', 'N4' => 'N4', 'N3' => 'N3', 'N2' => 'N2', 'N1' => 'N1', 'JFT Basic A2' => 'JFT Basic A2']),
                Tables\Filters\SelectFilter::make('matching_status')
                    ->label('Status Matching')
                    ->options([
                        'process_matching' => 'Proses Matching',
                        'matched' => 'Matched',
                        'waiting_result' => 'Menunggu Hasil',
                        'not_matched' => 'Belum Matching',
                        'cancelled' => 'Dibatalkan',
                    ]),
                Tables\Filters\SelectFilter::make('participant_status')
                    ->label('Status Peserta')
                    ->options(['ex' => 'Eks', 'new_comer' => 'New Comer']),
                Tables\Filters\SelectFilter::make('pathway')
                    ->label('Jalur')
                    ->options(['mandiri' => 'Mandiri', 'lpk' => 'LPK']),
                Tables\Filters\SelectFilter::make('sswCategories')
                    ->label('SSW')
                    ->relationship('sswCategories', 'name'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\Action::make('changeMatchingStatus')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color(fn ($record): string => match ($record->matching_status) {
                        'matched' => 'success',
                        'process_matching' => 'warning',
                        'waiting_result' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->form([
                        Forms\Components\Select::make('matching_status')
                            ->label('Status Matching')
                            ->options([
                                'process_matching' => 'Proses Matching',
                                'matched' => 'Matched',
                                'waiting_result' => 'Menunggu Hasil',
                                'not_matched' => 'Belum Matching',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('matched_company_name')
                            ->label('Nama Perusahaan')
                            ->requiredIf('matching_status', 'matched')
                            ->visible(fn (Get $get) => $get('matching_status') === 'matched')
                            ->maxLength(255),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update($data);
                        Notification::make()
                            ->title('Status matching berhasil diubah')
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}

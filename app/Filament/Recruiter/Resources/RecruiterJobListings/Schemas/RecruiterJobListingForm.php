<?php

namespace App\Filament\Recruiter\Resources\RecruiterJobListings\Schemas;

use App\Enums\JlptLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Models\SswCategory;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RecruiterJobListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Lowongan')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Lowongan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(4),
                        Forms\Components\Textarea::make('requirements')
                            ->label('Persyaratan')
                            ->rows(3),
                    ])->columns(1),

                Section::make('Perusahaan & Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => auth()->user()->company_name),
                        Forms\Components\Textarea::make('company_description')
                            ->label('Deskripsi Perusahaan')
                            ->rows(2),
                        Forms\Components\Select::make('location')
                            ->label('Prefektur')
                            ->options(config('prefectures.all'))
                            ->required()
                            ->searchable(),
                    ])->columns(2),

                Section::make('Thumbnail')
                    ->schema([
                        Forms\Components\TextInput::make('thumbnail_url')
                            ->label('Link Gambar (Google Drive / URL)')
                            ->url()
                            ->nullable()
                            ->maxLength(2000)
                            ->placeholder('https://drive.google.com/file/d/...'),
                    ]),

                Section::make('Gaji & Persyaratan')
                    ->schema([
                        Forms\Components\TextInput::make('salary_min')
                            ->label('Gaji Minimum (JPY)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0),
                        Forms\Components\TextInput::make('salary_max')
                            ->label('Gaji Maksimum (JPY)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0),
                        Forms\Components\Select::make('ssw_category_id')
                            ->label('Kategori SSW')
                            ->options(SswCategory::pluck('name', 'id'))
                            ->nullable()
                            ->visible(fn (Get $get): bool => $get('job_type') === JobType::TokuteiGinou->value)
                            ->required(fn (Get $get): bool => $get('job_type') === JobType::TokuteiGinou->value)
                            ->preload(),
                        Forms\Components\Select::make('job_type')
                            ->label('Jenis Lowongan')
                            ->options(JobType::options())
                            ->live()
                            ->required()
                            ->default(JobType::TokuteiGinou->value),
                        Forms\Components\Select::make('jlpt_level_required')
                            ->label('Level JLPT Diperlukan')
                            ->options(JlptLevel::options())
                            ->nullable(),
                        Forms\Components\Select::make('participant_status_required')
                            ->label('Status Peserta')
                            ->options(['any' => 'Semua', 'ex' => 'Eks', 'new_comer' => 'New Comer'])
                            ->required(),
                    ])->columns(3),

                Section::make('Pengaturan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(JobStatus::optionsExcept(JobStatus::Filled))
                            ->required()
                            ->default(JobStatus::Draft->value),
                        Forms\Components\DatePicker::make('deadline')
                            ->label('Deadline')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }
}

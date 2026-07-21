<?php

namespace App\Filament\Resources\JobListings\Schemas;

use App\Models\SswCategory;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class JobListingForm
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
                            ->maxLength(255),
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
                            ->visible(fn (Get $get): bool => $get('job_type') === 'tg')
                            ->required(fn (Get $get): bool => $get('job_type') === 'tg')
                            ->preload(),
                        Forms\Components\Select::make('job_type')
                            ->label('Jenis Lowongan')
                            ->options([
                                'magang' => 'Magang',
                                'tg' => 'Tokutei Ginou (SSW)',
                                'engineer' => 'Engineer / Gijinkoku',
                            ])
                            ->live()
                            ->required()
                            ->default('tg'),
                        Forms\Components\Select::make('jlpt_level_required')
                            ->label('Level JLPT Diperlukan')
                            ->options(['N5' => 'N5', 'N4' => 'N4', 'N3' => 'N3', 'N2' => 'N2', 'N1' => 'N1', 'JFT Basic A2' => 'JFT Basic A2'])
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
                            ->options([
                                'draft' => 'Draft',
                                'open' => 'Dibuka',
                                'closed' => 'Ditutup',
                                'filled' => 'Terisi',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\DatePicker::make('deadline')
                            ->label('Deadline')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }
}

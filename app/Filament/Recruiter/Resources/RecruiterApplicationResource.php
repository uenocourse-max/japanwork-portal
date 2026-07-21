<?php

namespace App\Filament\Recruiter\Resources;

use App\Filament\Recruiter\Resources\RecruiterApplications\Pages\ListRecruiterApplications;
use App\Filament\Recruiter\Resources\RecruiterApplications\Tables\RecruiterApplicationsTable;
use App\Models\JobApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RecruiterApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Lowongan';

    protected static ?string $navigationLabel = 'Lamaran Masuk';

    protected static ?string $modelLabel = 'Lamaran';

    protected static ?string $pluralModelLabel = 'Lamaran';

    public static function table(Table $table): Table
    {
        return RecruiterApplicationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('jobListing', fn ($query) => $query->where('posted_by', auth()->id()));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecruiterApplications::route('/'),
        ];
    }
}

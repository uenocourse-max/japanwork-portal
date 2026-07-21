<?php

namespace App\Filament\Recruiter\Resources;

use App\Filament\Recruiter\Resources\RecruiterJobListings\Pages\CreateRecruiterJobListing;
use App\Filament\Recruiter\Resources\RecruiterJobListings\Pages\EditRecruiterJobListing;
use App\Filament\Recruiter\Resources\RecruiterJobListings\Pages\ListRecruiterJobListings;
use App\Filament\Recruiter\Resources\RecruiterJobListings\Schemas\RecruiterJobListingForm;
use App\Filament\Recruiter\Resources\RecruiterJobListings\Tables\RecruiterJobListingsTable;
use App\Models\JobListing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RecruiterJobListingResource extends Resource
{
    protected static ?string $model = JobListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Lowongan';

    protected static ?string $navigationLabel = 'Lowongan Saya';

    protected static ?string $modelLabel = 'Lowongan';

    protected static ?string $pluralModelLabel = 'Lowongan';

    public static function form(Schema $schema): Schema
    {
        return RecruiterJobListingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecruiterJobListingsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('posted_by', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecruiterJobListings::route('/'),
            'create' => CreateRecruiterJobListing::route('/create'),
            'edit' => EditRecruiterJobListing::route('/{record}/edit'),
        ];
    }
}

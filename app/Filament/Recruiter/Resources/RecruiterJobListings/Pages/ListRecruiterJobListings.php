<?php

namespace App\Filament\Recruiter\Resources\RecruiterJobListings\Pages;

use App\Filament\Recruiter\Resources\RecruiterJobListingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecruiterJobListings extends ListRecords
{
    protected static string $resource = RecruiterJobListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Recruiter\Resources\RecruiterJobListings\Pages;

use App\Filament\Recruiter\Resources\RecruiterJobListingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecruiterJobListing extends EditRecord
{
    protected static string $resource = RecruiterJobListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Recruiter\Resources\RecruiterJobListings\Pages;

use App\Filament\Recruiter\Resources\RecruiterJobListingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecruiterJobListing extends CreateRecord
{
    protected static string $resource = RecruiterJobListingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['posted_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

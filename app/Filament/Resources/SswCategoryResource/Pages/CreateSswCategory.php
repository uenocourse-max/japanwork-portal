<?php

namespace App\Filament\Resources\SswCategoryResource\Pages;

use App\Filament\Resources\SswCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSswCategory extends CreateRecord
{
    protected static string $resource = SswCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

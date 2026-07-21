<?php

namespace App\Filament\Resources\SswCategoryResource\Pages;

use App\Filament\Resources\SswCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSswCategory extends EditRecord
{
    protected static string $resource = SswCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

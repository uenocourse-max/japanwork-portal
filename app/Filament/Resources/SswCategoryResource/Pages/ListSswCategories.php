<?php

namespace App\Filament\Resources\SswCategoryResource\Pages;

use App\Filament\Resources\SswCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSswCategories extends ListRecords
{
    protected static string $resource = SswCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

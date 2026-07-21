<?php

namespace App\Filament\Resources\LpkTskResource\Pages;

use App\Filament\Resources\LpkTskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLpkTsks extends ListRecords
{
    protected static string $resource = LpkTskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

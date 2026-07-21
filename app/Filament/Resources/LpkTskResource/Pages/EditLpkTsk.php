<?php

namespace App\Filament\Resources\LpkTskResource\Pages;

use App\Filament\Resources\LpkTskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditLpkTsk extends EditRecord
{
    protected static string $resource = LpkTskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $record->update($data);

        if ($record->user) {
            $userUpdate = [
                'name' => $record->name,
                'email' => $record->email,
            ];

            if (! empty($data['password'])) {
                $userUpdate['password'] = $data['password'];
            }

            $record->user->update($userUpdate);
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

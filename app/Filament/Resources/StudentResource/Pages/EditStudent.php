<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Enums\MatchingStatus;
use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->getRecord()->load('user');

        $data['user'] = [
            'name' => $student->user?->name ?? '',
            'email' => $student->user?->email ?? '',
        ];

        $data['ssw_categories'] = $student->sswCategories->pluck('id')->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $userData = $data['user'] ?? [];
        unset($data['user']);

        Validator::make($userData, [
            'email' => 'required|email|unique:users,email,'.$record->user_id,
        ])->validate();

        if ($record->user) {
            $updateData = [
                'name' => $userData['name'] ?? $record->user->name,
                'email' => $userData['email'] ?? $record->user->email,
            ];

            if (! empty($userData['password'])) {
                $updateData['password'] = Hash::make($userData['password']);
            }

            $record->user->update($updateData);
        }

        $sswCategories = $data['ssw_categories'] ?? [];
        unset($data['ssw_categories']);

        if (($data['matching_status'] ?? '') !== MatchingStatus::Matched->value || empty($data['matched_company_name'])) {
            $data['matched_company_name'] = null;
        }

        $record->update($data);
        $record->sswCategories()->sync($sswCategories);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

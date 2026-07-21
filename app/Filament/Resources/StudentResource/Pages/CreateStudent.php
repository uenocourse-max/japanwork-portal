<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $userData = $data['user'] ?? [];
        unset($data['user']);

        Validator::make($userData, [
            'email' => 'required|email|unique:users,email',
        ])->validate();

        $user = User::create([
            'name' => $userData['name'] ?? '',
            'email' => $userData['email'] ?? '',
            'password' => Hash::make($userData['password'] ?? 'password'),
            'role' => 'student',
        ]);

        $data['user_id'] = $user->id;

        $sswCategories = $data['ssw_categories'] ?? [];
        unset($data['ssw_categories']);

        $record = static::getModel()::create($data);

        if ($sswCategories) {
            $record->sswCategories()->sync($sswCategories);
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

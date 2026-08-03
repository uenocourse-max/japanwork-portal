<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        Validator::make($data, [
            'email' => 'required|email|unique:users,email',
        ])->validate();

        $data['password'] = Hash::make($data['password']);

        $user = static::getModel()::make($data);
        $user->forceFill(['role' => $data['role'] ?? 'student']);
        $user->save();

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

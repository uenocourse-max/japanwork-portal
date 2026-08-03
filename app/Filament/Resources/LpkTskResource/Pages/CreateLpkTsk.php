<?php

namespace App\Filament\Resources\LpkTskResource\Pages;

use App\Filament\Resources\LpkTskResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateLpkTsk extends CreateRecord
{
    protected static string $resource = LpkTskResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::make([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $user->forceFill(['role' => 'recruiter']);
            $user->save();

            $data['password'] = Hash::make($data['password']);
            $data['user_id'] = $user->id;

            return static::getModel()::create($data);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

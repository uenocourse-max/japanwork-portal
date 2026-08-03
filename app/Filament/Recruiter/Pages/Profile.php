<?php

namespace App\Filament\Recruiter\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Profile extends Page
{
    protected static ?string $navigationLabel = 'Profil Akun';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.recruiter.pages.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Informasi Perusahaan')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Nama Perusahaan')
                            ->maxLength(255)
                            ->nullable(),
                        TextInput::make('phone_number')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(255)
                            ->nullable(),
                        TextInput::make('location')
                            ->label('Lokasi')
                            ->maxLength(255)
                            ->nullable(),
                    ])->columns(3),

                Section::make('Ubah Password')
                    ->description('Kosongkan jika tidak ingin mengubah password')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->maxLength(255)
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function fillForm(): void
    {
        $user = Auth::user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'company_name' => $user->company_name,
            'location' => $user->location,
            'password' => '',
            'password_confirmation' => '',
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone_number' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ];

        if (! empty($data['password'])) {
            $rules['password'] = 'nullable|string|min:8|max:255|confirmed';
        }

        Validator::make($data, $rules)->validate();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'location' => $data['location'] ?? null,
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        $this->data['password'] = '';
        $this->data['password_confirmation'] = '';
        $this->form->fill($this->data);

        Notification::make()
            ->title('Profil berhasil diperbarui')
            ->success()
            ->send();
    }
}

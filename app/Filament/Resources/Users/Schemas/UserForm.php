<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Models\ExtensionOfficer;
use App\Models\Farmer;
use App\Models\FarmUser;
use App\Models\SystemUser;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Schemas\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->description('User login credentials')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->helperText('Leave blank to keep current password when editing'),
                        Select::make('role')
                            ->label('User Role')
                            ->options([
                                UserRole::SYSTEM_USER => 'System User',
                                UserRole::FARMER => 'Farmer',
                                UserRole::EXTENSION_OFFICER => 'Extension Officer',
                                UserRole::VET => 'Veterinarian',
                                UserRole::FARM_INVITED_USER => 'Farm Invited User',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('roleId', null)),
                        Select::make('status')
                            ->label('Account Status')
                            ->options([
                                'active' => 'Active',
                                'notActive' => 'Not Active'
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Profile Information')
                    ->description('Linked profile reference for the selected role')
                    ->schema([
                        Placeholder::make('profile_note')
                            ->label('Important')
                            ->content('Select the corresponding person profile to link to this login account.')
                            ->visible(fn (Get $get) => filled($get('role'))),

                        Select::make('roleId')
                            ->label(fn (Get $get) => match ($get('role')) {
                                UserRole::FARMER => 'Farmer Profile',
                                UserRole::EXTENSION_OFFICER => 'Extension Officer Profile',
                                UserRole::FARM_INVITED_USER => 'Farm User Profile',
                                UserRole::SYSTEM_USER, UserRole::VET => 'System User Profile',
                                default => 'Linked Profile',
                            })
                            ->options(function (Get $get) {
                                $role = $get('role');
                                return match ($role) {
                                    UserRole::FARMER => Farmer::query()
                                        ->get()
                                        ->mapWithKeys(fn ($f) => [
                                            $f->id => trim("{$f->firstName} {$f->middleName} {$f->surname}") . ($f->phone1 ? " ({$f->phone1})" : ''),
                                        ])
                                        ->toArray(),
                                    UserRole::EXTENSION_OFFICER => ExtensionOfficer::query()
                                        ->get()
                                        ->mapWithKeys(fn ($e) => [
                                            $e->id => trim("{$e->firstName} {$e->lastName}") . ($e->phone ? " ({$e->phone})" : ''),
                                        ])
                                        ->toArray(),
                                    UserRole::FARM_INVITED_USER => FarmUser::query()
                                        ->get()
                                        ->mapWithKeys(fn ($fu) => [
                                            $fu->id => trim("{$fu->firstName} {$fu->middleName} {$fu->lastName}") . ($fu->phone ? " ({$fu->phone})" : ''),
                                        ])
                                        ->toArray(),
                                    UserRole::SYSTEM_USER, UserRole::VET => SystemUser::query()
                                        ->get()
                                        ->mapWithKeys(fn ($s) => [
                                            $s->id => trim("{$s->firstName} {$s->lastName}") . ($s->phone ? " ({$s->phone})" : ''),
                                        ])
                                        ->toArray(),
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Get $get) => filled($get('role')))
                            ->helperText('Select the corresponding profile to link to this login user account.'),
                    ])
            ]);
    }
}

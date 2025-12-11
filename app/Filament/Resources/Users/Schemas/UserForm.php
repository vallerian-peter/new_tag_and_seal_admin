<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
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
                    ->description('Complete the profile for the selected role')
                    ->schema([
                        // Show note about creating profile first
                        \Filament\Forms\Components\Placeholder::make('profile_note')
                            ->label('Important')
                            ->content('You must create the profile (Farmer or System User) first, then come back to create the User account with the profile ID as roleId.')
                            ->visible(fn (Get $get) => filled($get('role'))),

                        TextInput::make('roleId')
                            ->label(fn (Get $get) => match ($get('role')) {
                                UserRole::FARMER => 'Farmer Profile ID',
                                UserRole::SYSTEM_USER, UserRole::EXTENSION_OFFICER, UserRole::VET, UserRole::FARM_INVITED_USER => 'System User Profile ID',
                                default => 'Profile ID',
                            })
                            ->required()
                            ->numeric()
                            ->helperText(fn (Get $get) => match ($get('role')) {
                                UserRole::FARMER => 'Enter the ID from the Farmers table',
                                UserRole::SYSTEM_USER, UserRole::EXTENSION_OFFICER, UserRole::VET, UserRole::FARM_INVITED_USER => 'Create a System User profile first, then enter its ID here',
                                default => 'Select a role first',
                            })
                            ->visible(fn (Get $get) => filled($get('role'))),
                    ])
                    ->collapsible(),
            ]);
    }
}

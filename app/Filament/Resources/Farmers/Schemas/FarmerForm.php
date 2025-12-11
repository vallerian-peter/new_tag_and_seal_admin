<?php

namespace App\Filament\Resources\Farmers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FarmerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('farmerNo')
                    ->required(),
                TextInput::make('firstName')
                    ->required(),
                TextInput::make('middleName')
                    ->default(null),
                TextInput::make('surname')
                    ->required(),
                TextInput::make('phone1')
                    ->tel()
                    ->required(),
                TextInput::make('phone2')
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('physicalAddress')
                    ->default(null),
                TextInput::make('farmerOrganizationMembership')
                    ->default(null),
                DatePicker::make('dateOfBirth'),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                Select::make('identityCardTypeId')
                    ->label('Identity Card Type')
                    ->relationship('identityCardType', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                TextInput::make('identityNumber')
                    ->default(null),
                Select::make('streetId')
                    ->label('Street')
                    ->relationship('street', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('schoolLevelId')
                    ->label('School Level')
                    ->relationship('schoolLevel', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('villageId')
                    ->label('Village')
                    ->relationship('village', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('wardId')
                    ->label('Ward')
                    ->relationship('ward', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('districtId')
                    ->label('District')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('regionId')
                    ->label('Region')
                    ->relationship('region', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('countryId')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->default(1)
                    ->required(),
                Select::make('farmerType')
                    ->options(['individual' => 'Individual', 'organization' => 'Organization'])
                    ->required(),
                Select::make('createdBy')
                    ->label('Created By')
                    ->relationship('creator', 'username')
                    ->getOptionLabelFromRecordUsing(fn ($record) => method_exists($record, 'getFilamentName') ? $record->getFilamentName() : ($record->username ?? $record->email ?? 'User'))
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('status')
                    ->options(['active' => 'Active', 'notActive' => 'Not active'])
                    ->default('active')
                    ->required(),
            ]);
    }
}

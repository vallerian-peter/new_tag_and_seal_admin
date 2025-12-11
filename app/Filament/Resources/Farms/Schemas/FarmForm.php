<?php

namespace App\Filament\Resources\Farms\Schemas;

use App\Support\UuidHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FarmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('farmerId')
                    ->label('Farmer')
                    ->relationship('farmer', 'firstName')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->firstName ?? '') . ' ' . ($record->surname ?? '')))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('uuid')
                    ->label('UUID')
                    ->default(fn () => UuidHelper::generate())
                    ->readOnly()
                    ->required(),
                TextInput::make('referenceNo')
                    ->required(),
                TextInput::make('regionalRegNo')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('size')
                    ->default(null),
                Select::make('sizeUnit')
                    ->options([
                        'acre' => 'Acre',
                        'hectare' => 'Hectare',
                        'square_meter' => 'Square meter',
                        'square_kilometer' => 'Square kilometer',
                    ])
                    ->default('acre')
                    ->required(),
                TextInput::make('latitudes')
                    ->default(null),
                TextInput::make('longitudes')
                    ->default(null),
                TextInput::make('physicalAddress')
                    ->required(),
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
                Select::make('legalStatusId')
                    ->label('Legal Status')
                    ->relationship('legalStatus', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('status')
                    ->options(['active' => 'Active', 'not-active' => 'Not active'])
                    ->default('active')
                    ->required(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Livestocks\Schemas;

use App\Support\UuidHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LivestockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('uuid')
                    ->label('UUID')
                    ->default(fn () => UuidHelper::generate())
                    ->readOnly()
                    ->required(),
                TextInput::make('identificationNumber')
                    ->required(),
                TextInput::make('dummyTagId')
                    ->default(null),
                TextInput::make('barcodeTagId')
                    ->default(null),
                TextInput::make('rfidTagId')
                    ->default(null),
                Select::make('livestockTypeId')
                    ->label('Livestock Type')
                    ->relationship('livestockType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                DatePicker::make('dateOfBirth')
                    ->required(),
                Select::make('motherUuid')
                    ->label('Mother')
                    ->relationship('mother', 'identificationNumber')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->identificationNumber ?? $record->uuid) . ' ' . ($record->name ? "- {$record->name}" : '')))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('fatherUuid')
                    ->label('Father')
                    ->relationship('father', 'identificationNumber')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->identificationNumber ?? $record->uuid) . ' ' . ($record->name ? "- {$record->name}" : '')))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                Select::make('breedId')
                    ->label('Breed')
                    ->relationship('breed', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('speciesId')
                    ->label('Species')
                    ->relationship('species', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(['active' => 'Active', 'notActive' => 'Not active'])
                    ->default('active')
                    ->required(),
                Select::make('livestockObtainedMethodId')
                    ->label('Obtained Method')
                    ->relationship('livestockObtainedMethod', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DatePicker::make('dateFirstEnteredToFarm'),
                TextInput::make('weightAsOnRegistration')
                    ->default(null),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Vaccines\Schemas;

use App\Models\VaccineSchedule;
use App\Support\UuidHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VaccineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->default(fn () => UuidHelper::generate())
                    ->readOnly()
                    ->required()
                    ->maxLength(255),
                Select::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Vaccine Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('lot')
                    ->label('Lot Number')
                    ->required()
                    ->maxLength(255),
                Select::make('formulationType')
                    ->label('Formulation Type')
                    ->options([
                        'live-attenuated' => 'Live Attenuated',
                        'inactivated' => 'Inactivated',
                    ])
                    ->required(),
                TextInput::make('dose')
                    ->label('Dose')
                    ->nullable(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                    ])
                    ->default('active')
                    ->required(),
                Select::make('vaccineTypeId')
                    ->label('Vaccine Type')
                    ->relationship('vaccineType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('vaccineSchedule')
                    ->label('Vaccine Schedule')
                    ->options(fn () => VaccineSchedule::query()
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->nullable()
                    ->searchable(),
            ]);
    }
}


<?php

namespace App\Filament\Resources\Transfers\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Support\UuidHelper;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransferForm
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
                    ->label('From Farm')
                    ->relationship('fromFarm', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('toFarmUuid')
                    ->label('To Farm')
                    ->relationship('toFarm', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('livestockUuid')
                    ->label('Livestock')
                    ->relationship('livestock', 'identificationNumber')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->identificationNumber ?? $record->uuid) . ' ' . ($record->name ? "- {$record->name}" : '')))
                    ->searchable()
                    ->preload()
                    ->required(),
                EventLogFormHelpers::eventDateField(),
                TextInput::make('transporterId')
                    ->label('Transporter ID')
                    ->nullable(),
                TextInput::make('reason')
                    ->label('Reason')
                    ->nullable(),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->nullable(),
                DatePicker::make('transferDate')
                    ->label('Transfer Date')
                    ->native(false)
                    ->nullable()
                    ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                    ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->nullable(),
                TextInput::make('status')
                    ->label('Status')
                    ->nullable(),
            ]);
    }
}


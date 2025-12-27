<?php

namespace App\Filament\Resources\Milkings\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MilkingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('milking'),
                EventLogFormHelpers::farmField(),
                EventLogFormHelpers::livestockField()
                    ->preload()
                    ->required(),
                EventLogFormHelpers::eventDateField(),
                Select::make('milkingMethodId')
                    ->label('Milking Method')
                    ->relationship('milkingMethod', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('amount')
                    ->label('Amount (with unit)')
                    ->nullable(),
                TextInput::make('lactometerReading')
                    ->label('Lactometer Reading')
                    ->nullable(),
                TextInput::make('correctedLactometerReading')
                    ->label('Corrected Lactometer Reading')
                    ->nullable(),
                TextInput::make('solid')
                    ->label('Solid %')
                    ->nullable(),
                TextInput::make('solidNonFat')
                    ->label('Solid Non Fat %')
                    ->nullable(),
                TextInput::make('protein')
                    ->label('Protein %')
                    ->nullable(),
                TextInput::make('totalSolids')
                    ->label('Total Solids %')
                    ->nullable(),
                TextInput::make('colonyFormingUnits')
                    ->label('CFU')
                    ->nullable(),
                TextInput::make('acidity')
                    ->label('Acidity')
                    ->nullable(),
                TextInput::make('session')
                    ->label('Session')
                    ->placeholder('Morning / Afternoon / Night')
                    ->nullable(),
                TextInput::make('status')
                    ->label('Status')
                    ->nullable(),
            ]);
    }
}


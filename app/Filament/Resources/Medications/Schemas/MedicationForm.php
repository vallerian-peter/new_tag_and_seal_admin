<?php

namespace App\Filament\Resources\Medications\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Support\UuidHelper;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MedicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('medication'),
                EventLogFormHelpers::farmField(),
                EventLogFormHelpers::livestockField(),
                Select::make('diseaseId')
                    ->label('Disease')
                    ->relationship('disease', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('medicineId')
                    ->label('Medicine')
                    ->relationship('medicine', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->helperText('Include unit, e.g. 10ml')
                    ->nullable(),
                TextInput::make('withdrawalPeriod')
                    ->label('Withdrawal Period')
                    ->helperText('Include duration and unit, e.g. 7 days')
                    ->nullable(),
                DatePicker::make('medicationDate')
                    ->label('Medication Date')
                    ->afterStateHydrated(function (DatePicker $component, $state): void {
                        if (blank($state) || $state instanceof Carbon) {
                            return;
                        }

                        try {
                            $component->state(Carbon::parse($state));
                        } catch (\Throwable) {
                            $component->state(null);
                        }
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (blank($state)) {
                            return null;
                        }

                        if ($state instanceof Carbon) {
                            return $state->format('Y-m-d');
                        }

                        return (string) $state;
                    }),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->nullable(),
            ]);
    }
}


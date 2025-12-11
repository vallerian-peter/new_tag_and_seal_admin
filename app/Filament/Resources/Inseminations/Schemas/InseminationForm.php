<?php

namespace App\Filament\Resources\Inseminations\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Models\Country;
use App\Models\HeatType;
use App\Models\InseminationService;
use App\Models\SemenStrawType;
use App\Support\UuidHelper;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InseminationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('insemination'),
                EventLogFormHelpers::farmField(),
                EventLogFormHelpers::livestockField()
                    ->preload()
                    ->required(),
                DatePicker::make('lastHeatDate')
                    ->label('Last Heat Date')
                    ->native(false)
                    ->nullable()
                    ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                    ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                Select::make('currentHeatTypeId')
                    ->label('Current Heat Type')
                    ->relationship('currentHeatType', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Manage heat types under Logs Reference Data.'),
                Select::make('inseminationServiceId')
                    ->label('Insemination Service')
                    ->relationship('inseminationService', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('semenStrawTypeId')
                    ->label('Semen Straw Type')
                    ->relationship('semenStrawType', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DatePicker::make('inseminationDate')
                    ->label('Insemination Date')
                    ->native(false)
                    ->nullable()
                    ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                    ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                TextInput::make('bullCode')
                    ->label('Bull Code')
                    ->nullable(),
                TextInput::make('bullBreed')
                    ->label('Bull Breed')
                    ->nullable(),
                DatePicker::make('semenProductionDate')
                    ->label('Semen Production Date')
                    ->native(false)
                    ->nullable()
                    ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                    ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                Select::make('productionCountry')
                    ->label('Production Country')
                    ->options(function () {
                        return Country::query()
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(function ($country) {
                                $label = $country->name;
                                if ($country->shortName) {
                                    $label .= ' (' . $country->shortName . ')';
                                }
                                return [$country->name => $label];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->nullable()
                    ->helperText('Select the country where the semen was produced.'),
                TextInput::make('semenBatchNumber')
                    ->label('Semen Batch Number')
                    ->nullable(),
                TextInput::make('internationalId')
                    ->label('International ID')
                    ->nullable(),
                TextInput::make('aiCode')
                    ->label('AI Code')
                    ->nullable(),
                TextInput::make('manufacturerName')
                    ->label('Manufacturer Name')
                    ->nullable(),
                TextInput::make('semenSupplier')
                    ->label('Semen Supplier')
                    ->nullable(),
            ]);
    }
}


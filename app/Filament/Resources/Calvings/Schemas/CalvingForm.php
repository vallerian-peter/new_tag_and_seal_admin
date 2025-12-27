<?php

namespace App\Filament\Resources\Calvings\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CalvingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('calving'),
                EventLogFormHelpers::farmField(),
                EventLogFormHelpers::livestockField()
                    ->preload()
                    ->required(),
                EventLogFormHelpers::eventDateField(),
                DatePicker::make('startDate')
                    ->label('Start Date')
                    ->native(false)
                    ->nullable()
                    ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                    ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                DatePicker::make('endDate')
                    ->label('End Date')
                    ->native(false)
                    ->nullable()
                    ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                    ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                Select::make('calvingTypeId')
                    ->label('Calving Type')
                    ->relationship('calvingType', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('calvingProblemsId')
                    ->label('Calving Problem')
                    ->relationship('calvingProblem', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('reproductiveProblemId')
                    ->label('Reproductive Problem')
                    ->relationship('reproductiveProblem', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
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


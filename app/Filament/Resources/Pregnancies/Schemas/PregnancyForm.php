<?php

namespace App\Filament\Resources\Pregnancies\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PregnancyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('pregnancy'),
                EventLogFormHelpers::farmField(),
                EventLogFormHelpers::livestockField()
                    ->preload()
                    ->required(),
                Select::make('testResultId')
                    ->label('Pregnancy Test Result')
                    ->relationship('testResult', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('noOfMonths')
                    ->label('Number of Months')
                    ->numeric()
                    ->nullable(),
                DatePicker::make('testDate')
                    ->label('Test Date')
                    ->native(false)
                    ->nullable()
                    ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                    ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                TextInput::make('status')
                    ->label('Status')
                    ->nullable(),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->nullable(),
            ]);
    }
}


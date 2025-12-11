<?php

namespace App\Filament\Resources\Dryoffs\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DryoffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('dryoff'),
                EventLogFormHelpers::farmField(),
                EventLogFormHelpers::livestockField()
                    ->preload()
                    ->required(),
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
                Textarea::make('reason')
                    ->label('Reason')
                    ->rows(3)
                    ->nullable(),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->nullable(),
            ]);
    }
}


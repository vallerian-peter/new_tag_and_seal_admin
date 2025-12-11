<?php

namespace App\Filament\Resources\WeightChanges\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Support\UuidHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WeightChangeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('weight'),
                EventLogFormHelpers::farmField(),
                EventLogFormHelpers::livestockField(),
                TextInput::make('oldWeight')
                    ->label('Old Weight (kg)')
                    ->numeric()
                    ->required(),
                TextInput::make('newWeight')
                    ->label('New Weight (kg)')
                    ->numeric()
                    ->required(),
                Textarea::make('remarks')
                    ->rows(3)
                    ->maxLength(65535)
                    ->nullable(),
            ]);
    }
}



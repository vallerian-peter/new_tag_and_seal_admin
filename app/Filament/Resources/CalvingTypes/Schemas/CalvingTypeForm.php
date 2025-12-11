<?php

namespace App\Filament\Resources\CalvingTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CalvingTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Calving Type Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}


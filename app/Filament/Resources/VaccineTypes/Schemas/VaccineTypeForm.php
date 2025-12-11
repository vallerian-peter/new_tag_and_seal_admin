<?php

namespace App\Filament\Resources\VaccineTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VaccineTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Vaccine Type Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}


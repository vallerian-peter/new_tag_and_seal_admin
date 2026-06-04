<?php

namespace App\Filament\Resources\LivestockMarkingTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LivestockMarkingTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Type Name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
        ]);
    }
}

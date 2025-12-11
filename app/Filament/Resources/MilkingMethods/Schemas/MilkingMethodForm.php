<?php

namespace App\Filament\Resources\MilkingMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MilkingMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Method Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}


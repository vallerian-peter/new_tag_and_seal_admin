<?php

namespace App\Filament\Resources\TeethClippingMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeethClippingMethodForm
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


<?php

namespace App\Filament\Resources\InseminationServices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InseminationServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Service Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}


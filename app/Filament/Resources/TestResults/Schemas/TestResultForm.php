<?php

namespace App\Filament\Resources\TestResults\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TestResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Result Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}


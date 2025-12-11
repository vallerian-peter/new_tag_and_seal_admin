<?php

namespace App\Filament\Resources\CalvingProblems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CalvingProblemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Calving Problem Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}


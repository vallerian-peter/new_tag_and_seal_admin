<?php

namespace App\Filament\Resources\ReproductiveProblems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReproductiveProblemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Problem Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}


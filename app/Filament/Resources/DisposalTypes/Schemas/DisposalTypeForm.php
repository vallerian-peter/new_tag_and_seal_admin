<?php

namespace App\Filament\Resources\DisposalTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DisposalTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Disposal Type Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}


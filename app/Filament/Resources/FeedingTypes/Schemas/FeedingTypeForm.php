<?php

namespace App\Filament\Resources\FeedingTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FeedingTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Feeding Type Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}



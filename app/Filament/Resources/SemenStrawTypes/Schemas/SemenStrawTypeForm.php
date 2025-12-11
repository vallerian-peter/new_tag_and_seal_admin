<?php

namespace App\Filament\Resources\SemenStrawTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SemenStrawTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Straw Type Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('category')
                    ->label('Category')
                    ->nullable()
                    ->maxLength(255),
            ]);
    }
}


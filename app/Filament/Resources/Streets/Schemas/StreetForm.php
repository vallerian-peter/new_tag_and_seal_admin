<?php

namespace App\Filament\Resources\Streets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StreetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('wardId')
                    ->label('Ward')
                    ->relationship('ward', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\BirthTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BirthTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Birth Type Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('livestockTypeId')
                    ->label('Livestock Type')
                    ->relationship('livestockType', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Leave empty for generic birth types that apply to all livestock types'),
            ]);
    }
}


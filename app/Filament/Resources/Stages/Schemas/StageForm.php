<?php

namespace App\Filament\Resources\Stages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Stage Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('livestockTypeId')
                    ->label('Livestock Type')
                    ->relationship('livestockType', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Stages are specific to livestock types (e.g., Calf for Cattle, Piglet for Swine)'),
            ]);
    }
}


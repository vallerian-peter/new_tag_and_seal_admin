<?php

namespace App\Filament\Resources\Diseases\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DiseaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Disease Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'spreadable' => 'Spreadable',
                        'non-spreadable' => 'Non-spreadable',
                    ])
                    ->required(),
            ]);
    }
}


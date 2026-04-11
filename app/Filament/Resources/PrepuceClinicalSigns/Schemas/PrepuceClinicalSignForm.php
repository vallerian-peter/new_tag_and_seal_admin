<?php

namespace App\Filament\Resources\PrepuceClinicalSigns\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrepuceClinicalSignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Label (EN)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('name_sw')
                    ->label('Label (SW)')
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }
}

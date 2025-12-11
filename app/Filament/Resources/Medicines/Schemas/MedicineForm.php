<?php

namespace App\Filament\Resources\Medicines\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MedicineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Medicine Name')
                    ->required()
                    ->maxLength(255),
                Select::make('medicineTypeId')
                    ->label('Medicine Type')
                    ->relationship('medicineType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}



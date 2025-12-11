<?php

namespace App\Filament\Resources\AdministrationRoutes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdministrationRouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Route Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}



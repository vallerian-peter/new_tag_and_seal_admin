<?php

namespace App\Filament\Resources\IdentityCardTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IdentityCardTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}

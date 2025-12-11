<?php

namespace App\Filament\Resources\SystemUsers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SystemUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('firstName')
                    ->required(),
                TextInput::make('middleName')
                    ->default(null),
                TextInput::make('lastName')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('address')
                    ->default(null),
                Select::make('createdBy')
                    ->label('Created By')
                    ->relationship('creator', 'username')
                    ->getOptionLabelFromRecordUsing(fn ($record) => method_exists($record, 'getFilamentName') ? $record->getFilamentName() : ($record->username ?? $record->email ?? 'User'))
                    ->searchable()
                    ->preload()
                    ->default(null),
            ]);
    }
}

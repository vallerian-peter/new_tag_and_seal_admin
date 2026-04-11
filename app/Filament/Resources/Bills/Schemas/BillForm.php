<?php

namespace App\Filament\Resources\Bills\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required()
                    ->maxLength(255)
                    ->disabledOn('edit'),
                TextInput::make('billNo')
                    ->label('Bill Number')
                    ->maxLength(30)
                    ->unique(ignoreRecord: true),
                Select::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('farmerId')
                    ->label('Farmer')
                    ->relationship('farmer', 'firstName')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->firstName ?? '').' '.($record->lastName ?? '')))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('extensionOfficerId')
                    ->label('Extension Officer ID')
                    ->numeric()
                    ->nullable(),
                TextInput::make('subjectType')
                    ->label('Subject Type')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('subjectUuid')
                    ->label('Subject UUID')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                    ])
                    ->default('pending')
                    ->required(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->nullable(),
            ]);
    }
}

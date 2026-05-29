<?php

namespace App\Filament\Resources\FinanceIncomes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FinanceIncomeForm
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
                TextInput::make('sourceType')
                    ->label('Source Type')
                    ->maxLength(255),
                TextInput::make('sourceUuid')
                    ->label('Source UUID')
                    ->maxLength(255),
                Select::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload(),
                Select::make('farmerId')
                    ->label('Farmer')
                    ->relationship('farmer', 'firstName')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->firstName ?? '').' '.($record->surname ?? '')))
                    ->searchable()
                    ->preload(),
                TextInput::make('referenceNo')
                    ->label('Reference No')
                    ->maxLength(255),
                TextInput::make('subjectType')
                    ->label('Subject Type')
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                TextInput::make('unitAmount')
                    ->label('Unit Amount')
                    ->numeric()
                    ->required(),
                TextInput::make('totalAmount')
                    ->label('Total Amount')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'received' => 'Received',
                    ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('incomeDate')
                    ->label('Income Date')
                    ->seconds(false),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]);
    }
}

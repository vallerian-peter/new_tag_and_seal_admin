<?php

namespace App\Filament\Resources\FinanceExpenses\Schemas;

use App\Support\UuidHelper;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FinanceExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('uuid')
                    ->default(fn () => UuidHelper::generate())
                    ->required(),
                TextInput::make('sourceType')
                    ->label('Source Type')
                    ->maxLength(255),
                Hidden::make('sourceUuid'),
                Select::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?: ($record->referenceNo ?: 'Farm #' . $record->id)) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload(),
                Select::make('farmerId')
                    ->label('Farmer')
                    ->relationship('farmer', 'firstName')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->firstName ?? '').' '.($record->surname ?? '')))
                    ->searchable(['firstName', 'surname'])
                    ->preload(),
                TextInput::make('billNo')
                    ->label('Bill No')
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
                TextInput::make('unitCost')
                    ->label('Unit Cost')
                    ->numeric()
                    ->required(),
                TextInput::make('totalCost')
                    ->label('Total Cost')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'completed' => 'Completed',
                    ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('expenseDate')
                    ->label('Expense Date')
                    ->seconds(false),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]);
    }
}

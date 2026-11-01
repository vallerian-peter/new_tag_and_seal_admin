<?php

namespace App\Filament\Resources\FinanceExpenses;

use App\Filament\Resources\FinanceExpenses\Pages\CreateFinanceExpense;
use App\Filament\Resources\FinanceExpenses\Pages\EditFinanceExpense;
use App\Filament\Resources\FinanceExpenses\Pages\ListFinanceExpenses;
use App\Filament\Resources\FinanceExpenses\Schemas\FinanceExpenseForm;
use App\Filament\Resources\FinanceExpenses\Tables\FinanceExpensesTable;
use App\Models\FinanceExpense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class FinanceExpenseResource extends Resource
{
    protected static ?string $model = FinanceExpense::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static UnitEnum|string|null $navigationGroup = 'Finance & Billing';

    protected static ?string $navigationLabel = 'Expenditure';

    protected static ?string $modelLabel = 'Expenditure';

    protected static ?string $pluralModelLabel = 'Expenditures';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return FinanceExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceExpensesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanceExpenses::route('/'),
            'create' => CreateFinanceExpense::route('/create'),
            'edit' => EditFinanceExpense::route('/{record}/edit'),
        ];
    }
}

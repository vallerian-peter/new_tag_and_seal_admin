<?php

namespace App\Filament\Resources\FinanceIncomes;

use App\Filament\Resources\FinanceIncomes\Pages\CreateFinanceIncome;
use App\Filament\Resources\FinanceIncomes\Pages\EditFinanceIncome;
use App\Filament\Resources\FinanceIncomes\Pages\ListFinanceIncomes;
use App\Filament\Resources\FinanceIncomes\Schemas\FinanceIncomeForm;
use App\Filament\Resources\FinanceIncomes\Tables\FinanceIncomesTable;
use App\Models\FinanceIncome;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class FinanceIncomeResource extends Resource
{
    protected static ?string $model = FinanceIncome::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static UnitEnum|string|null $navigationGroup = 'Bills and Report';

    protected static ?string $navigationLabel = 'Income';

    protected static ?string $modelLabel = 'Income';

    protected static ?string $pluralModelLabel = 'Incomes';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return FinanceIncomeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceIncomesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanceIncomes::route('/'),
            'create' => CreateFinanceIncome::route('/create'),
            'edit' => EditFinanceIncome::route('/{record}/edit'),
        ];
    }
}

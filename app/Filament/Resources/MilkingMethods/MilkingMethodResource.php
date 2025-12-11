<?php

namespace App\Filament\Resources\MilkingMethods;

use App\Filament\Resources\MilkingMethods\Pages\CreateMilkingMethod;
use App\Filament\Resources\MilkingMethods\Pages\EditMilkingMethod;
use App\Filament\Resources\MilkingMethods\Pages\ListMilkingMethods;
use App\Filament\Resources\MilkingMethods\Schemas\MilkingMethodForm;
use App\Filament\Resources\MilkingMethods\Tables\MilkingMethodsTable;
use App\Models\MilkingMethod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class MilkingMethodResource extends Resource
{
    protected static ?string $model = MilkingMethod::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Milking Methods';

    protected static ?string $modelLabel = 'Milking Method';

    protected static ?string $pluralModelLabel = 'Milking Methods';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return MilkingMethodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MilkingMethodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMilkingMethods::route('/'),
            'create' => CreateMilkingMethod::route('/create'),
            'edit' => EditMilkingMethod::route('/{record}/edit'),
        ];
    }
}


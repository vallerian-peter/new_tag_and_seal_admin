<?php

namespace App\Filament\Resources\TailDockingMethods;

use App\Filament\Resources\TailDockingMethods\Pages\CreateTailDockingMethod;
use App\Filament\Resources\TailDockingMethods\Pages\EditTailDockingMethod;
use App\Filament\Resources\TailDockingMethods\Pages\ListTailDockingMethods;
use App\Filament\Resources\TailDockingMethods\Schemas\TailDockingMethodForm;
use App\Filament\Resources\TailDockingMethods\Tables\TailDockingMethodsTable;
use App\Models\TailDockingMethod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TailDockingMethodResource extends Resource
{
    protected static ?string $model = TailDockingMethod::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static UnitEnum|string|null $navigationGroup = 'Reference Master Data';

    protected static ?string $navigationLabel = 'Tail Docking Methods';

    protected static ?string $modelLabel = 'Tail Docking Method';

    protected static ?string $pluralModelLabel = 'Tail Docking Methods';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return TailDockingMethodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TailDockingMethodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTailDockingMethods::route('/'),
            'create' => CreateTailDockingMethod::route('/create'),
            'edit' => EditTailDockingMethod::route('/{record}/edit'),
        ];
    }
}

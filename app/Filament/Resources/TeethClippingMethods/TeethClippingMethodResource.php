<?php

namespace App\Filament\Resources\TeethClippingMethods;

use App\Filament\Resources\TeethClippingMethods\Pages\CreateTeethClippingMethod;
use App\Filament\Resources\TeethClippingMethods\Pages\EditTeethClippingMethod;
use App\Filament\Resources\TeethClippingMethods\Pages\ListTeethClippingMethods;
use App\Filament\Resources\TeethClippingMethods\Schemas\TeethClippingMethodForm;
use App\Filament\Resources\TeethClippingMethods\Tables\TeethClippingMethodsTable;
use App\Models\TeethClippingMethod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TeethClippingMethodResource extends Resource
{
    protected static ?string $model = TeethClippingMethod::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scissors';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Teeth Clipping Methods';

    protected static ?string $modelLabel = 'Teeth Clipping Method';

    protected static ?string $pluralModelLabel = 'Teeth Clipping Methods';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return TeethClippingMethodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeethClippingMethodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeethClippingMethods::route('/'),
            'create' => CreateTeethClippingMethod::route('/create'),
            'edit' => EditTeethClippingMethod::route('/{record}/edit'),
        ];
    }
}


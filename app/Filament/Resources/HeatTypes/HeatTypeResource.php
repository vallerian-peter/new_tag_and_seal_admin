<?php

namespace App\Filament\Resources\HeatTypes;

use App\Filament\Resources\HeatTypes\Pages\CreateHeatType;
use App\Filament\Resources\HeatTypes\Pages\EditHeatType;
use App\Filament\Resources\HeatTypes\Pages\ListHeatTypes;
use App\Filament\Resources\HeatTypes\Schemas\HeatTypeForm;
use App\Filament\Resources\HeatTypes\Tables\HeatTypesTable;
use App\Models\HeatType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class HeatTypeResource extends Resource
{
    protected static ?string $model = HeatType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Heat Types';

    protected static ?string $modelLabel = 'Heat Type';

    protected static ?string $pluralModelLabel = 'Heat Types';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return HeatTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HeatTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHeatTypes::route('/'),
            'create' => CreateHeatType::route('/create'),
            'edit' => EditHeatType::route('/{record}/edit'),
        ];
    }
}


<?php

namespace App\Filament\Resources\LivestockMarkingTypes;

use App\Filament\Resources\LivestockMarkingTypes\Pages\CreateLivestockMarkingType;
use App\Filament\Resources\LivestockMarkingTypes\Pages\EditLivestockMarkingType;
use App\Filament\Resources\LivestockMarkingTypes\Pages\ListLivestockMarkingTypes;
use App\Filament\Resources\LivestockMarkingTypes\Schemas\LivestockMarkingTypeForm;
use App\Filament\Resources\LivestockMarkingTypes\Tables\LivestockMarkingTypesTable;
use App\Models\LivestockMarkingType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LivestockMarkingTypeResource extends Resource
{
    protected static ?string $model = LivestockMarkingType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static UnitEnum|string|null $navigationGroup = 'Reference Master Data';

    protected static ?string $navigationLabel = 'Marking Types';

    protected static ?string $modelLabel = 'Marking Type';

    protected static ?string $pluralModelLabel = 'Marking Types';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return LivestockMarkingTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LivestockMarkingTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLivestockMarkingTypes::route('/'),
            'create' => CreateLivestockMarkingType::route('/create'),
            'edit' => EditLivestockMarkingType::route('/{record}/edit'),
        ];
    }
}

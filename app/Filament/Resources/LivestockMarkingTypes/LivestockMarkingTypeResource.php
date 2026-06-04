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

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Livestock Marking Types';

    protected static ?string $modelLabel = 'Livestock Marking Type';

    protected static ?string $pluralModelLabel = 'Livestock Marking Types';

    protected static ?int $navigationSort = 23;

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

<?php

namespace App\Filament\Resources\CalvingTypes;

use App\Filament\Resources\CalvingTypes\Pages\CreateCalvingType;
use App\Filament\Resources\CalvingTypes\Pages\EditCalvingType;
use App\Filament\Resources\CalvingTypes\Pages\ListCalvingTypes;
use App\Filament\Resources\CalvingTypes\Schemas\CalvingTypeForm;
use App\Filament\Resources\CalvingTypes\Tables\CalvingTypesTable;
use App\Models\CalvingType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CalvingTypeResource extends Resource
{
    protected static ?string $model = CalvingType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Calving Types';

    protected static ?string $modelLabel = 'Calving Type';

    protected static ?string $pluralModelLabel = 'Calving Types';

    protected static ?int $navigationSort = 10;

    // Hide from navigation - use BirthTypes instead
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return CalvingTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalvingTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalvingTypes::route('/'),
            'create' => CreateCalvingType::route('/create'),
            'edit' => EditCalvingType::route('/{record}/edit'),
        ];
    }
}


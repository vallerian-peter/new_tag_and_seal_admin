<?php

namespace App\Filament\Resources\BirthTypes;

use App\Filament\Resources\BirthTypes\Pages\CreateBirthType;
use App\Filament\Resources\BirthTypes\Pages\EditBirthType;
use App\Filament\Resources\BirthTypes\Pages\ListBirthTypes;
use App\Filament\Resources\BirthTypes\Schemas\BirthTypeForm;
use App\Filament\Resources\BirthTypes\Tables\BirthTypesTable;
use App\Models\BirthType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class BirthTypeResource extends Resource
{
    protected static ?string $model = BirthType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Birth Types';

    protected static ?string $modelLabel = 'Birth Type';

    protected static ?string $pluralModelLabel = 'Birth Types';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return BirthTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BirthTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBirthTypes::route('/'),
            'create' => CreateBirthType::route('/create'),
            'edit' => EditBirthType::route('/{record}/edit'),
        ];
    }
}


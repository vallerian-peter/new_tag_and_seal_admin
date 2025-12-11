<?php

namespace App\Filament\Resources\Streets;

use App\Filament\Resources\Streets\Pages\CreateStreet;
use App\Filament\Resources\Streets\Pages\EditStreet;
use App\Filament\Resources\Streets\Pages\ListStreets;
use App\Filament\Resources\Streets\Schemas\StreetForm;
use App\Filament\Resources\Streets\Tables\StreetsTable;
use App\Models\Street;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StreetResource extends Resource
{
    protected static ?string $model = Street::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static UnitEnum|string|null $navigationGroup = 'Geographical';

    protected static ?string $navigationLabel = 'Streets';

    protected static ?string $modelLabel = 'Street';

    protected static ?string $pluralModelLabel = 'Streets';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return StreetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StreetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStreets::route('/'),
            'create' => CreateStreet::route('/create'),
            'edit' => EditStreet::route('/{record}/edit'),
        ];
    }
}

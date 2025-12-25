<?php

namespace App\Filament\Resources\Farms;

use App\Filament\Resources\Farms\Pages;
use App\Filament\Resources\Farms\Pages\CreateFarm;
use App\Filament\Resources\Farms\Pages\EditFarm;
use App\Filament\Resources\Farms\Pages\ListFarms;
use App\Filament\Resources\Farms\Schemas\FarmForm;
use App\Filament\Resources\Farms\Tables\FarmsTable;
use App\Models\Farm;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FarmResource extends Resource
{
    protected static ?string $model = Farm::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static UnitEnum|string|null $navigationGroup = 'Livestock & Data';

    protected static ?string $navigationLabel = 'Farms';

    protected static ?string $modelLabel = 'Farm';

    protected static ?string $pluralModelLabel = 'Farms';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'referenceNo', 'regionalRegNo', 'physicalAddress'];
    }

    public static function form(Schema $schema): Schema
    {
        return FarmForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FarmsTable::configure($table);
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
            'index' => ListFarms::route('/'),
            'create' => CreateFarm::route('/create'),
            'view' => Pages\ViewFarm::route('/{record}'),
            'edit' => EditFarm::route('/{record}/edit'),
        ];
    }
}

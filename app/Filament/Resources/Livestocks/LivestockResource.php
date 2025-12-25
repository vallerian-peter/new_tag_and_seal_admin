<?php

namespace App\Filament\Resources\Livestocks;

use App\Filament\Resources\Livestocks\Pages;
use App\Filament\Resources\Livestocks\Pages\CreateLivestock;
use App\Filament\Resources\Livestocks\Pages\EditLivestock;
use App\Filament\Resources\Livestocks\Pages\ListLivestocks;
use App\Filament\Resources\Livestocks\Schemas\LivestockForm;
use App\Filament\Resources\Livestocks\Tables\LivestocksTable;
use App\Models\Livestock;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LivestockResource extends Resource
{
    protected static ?string $model = Livestock::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static UnitEnum|string|null $navigationGroup = 'Livestock & Data';

    protected static ?string $navigationLabel = 'Livestock';

    protected static ?string $modelLabel = 'Livestock';

    protected static ?string $pluralModelLabel = 'Livestock';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'identificationNumber';

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['identificationNumber', 'name', 'dummyTagId', 'barcodeTagId', 'rfidTagId'];
    }

    public static function form(Schema $schema): Schema
    {
        return LivestockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LivestocksTable::configure($table);
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
            'index' => ListLivestocks::route('/'),
            'create' => CreateLivestock::route('/create'),
            'view' => Pages\ViewLivestock::route('/{record}'),
            'edit' => EditLivestock::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Feedings;

use App\Filament\Resources\Feedings\Pages\CreateFeeding;
use App\Filament\Resources\Feedings\Pages\EditFeeding;
use App\Filament\Resources\Feedings\Pages\ListFeedings;
use App\Filament\Resources\Feedings\Schemas\FeedingForm;
use App\Filament\Resources\Feedings\Tables\FeedingsTable;
use App\Models\Feeding;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class FeedingResource extends Resource
{
    protected static ?string $model = Feeding::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Feedings';

    protected static ?string $modelLabel = 'Feeding';

    protected static ?string $pluralModelLabel = 'Feedings';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'uuid';

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        $record->loadMissing(['livestock', 'farm']);
        $livestockTag = $record->livestock?->identificationNumber ?? 'N/A';
        $farmName = $record->farm?->name ?? 'N/A';
        return "Feeding - {$livestockTag} ({$farmName})";
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['uuid', 'remarks', 'amount', 'livestock.identificationNumber', 'farm.name'];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['livestock', 'farm']);
    }

    public static function form(Schema $schema): Schema
    {
        return FeedingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeedingsTable::configure($table);
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
            'index' => ListFeedings::route('/'),
            'create' => CreateFeeding::route('/create'),
            'edit' => EditFeeding::route('/{record}/edit'),
        ];
    }
}



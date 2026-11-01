<?php

namespace App\Filament\Resources\LookupSets;

use App\Filament\Resources\LookupSets\Pages\CreateLookupSet;
use App\Filament\Resources\LookupSets\Pages\EditLookupSet;
use App\Filament\Resources\LookupSets\Pages\ListLookupSets;
use App\Filament\Resources\LookupSets\Schemas\LookupSetForm;
use App\Filament\Resources\LookupSets\Tables\LookupSetsTable;
use App\Models\LookupSet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class LookupSetResource extends Resource
{
    protected static ?string $model = LookupSet::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static UnitEnum|string|null $navigationGroup = 'Blueprint Engine';

    protected static ?string $navigationLabel = 'Lookup Sets';

    protected static ?string $modelLabel = 'Lookup Set';

    protected static ?string $pluralModelLabel = 'Lookup Sets';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code'];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPlatformPermission('configuration.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPlatformPermission('configuration.manage') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return (auth()->user()?->hasPlatformPermission('configuration.manage') ?? false)
            && ($record->status === 'draft' || $record->is_system || $record->is_extensible);
    }

    public static function form(Schema $schema): Schema
    {
        return LookupSetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LookupSetsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLookupSets::route('/'),
            'create' => CreateLookupSet::route('/create'),
            'view' => Pages\ViewLookupSet::route('/{record}') ,
            'edit' => EditLookupSet::route('/{record}/edit'),
        ];
    }
}

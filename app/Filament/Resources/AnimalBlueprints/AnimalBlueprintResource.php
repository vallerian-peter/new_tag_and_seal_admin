<?php

namespace App\Filament\Resources\AnimalBlueprints;

use App\Filament\Resources\AnimalBlueprints\Pages\CreateAnimalBlueprint;
use App\Filament\Resources\AnimalBlueprints\Pages\EditAnimalBlueprint;
use App\Filament\Resources\AnimalBlueprints\Pages\ListAnimalBlueprints;
use App\Filament\Resources\AnimalBlueprints\Pages\PreviewAnimalBlueprint;
use App\Filament\Resources\AnimalBlueprints\Schemas\AnimalBlueprintForm;
use App\Filament\Resources\AnimalBlueprints\Tables\AnimalBlueprintsTable;
use App\Models\AnimalBlueprint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AnimalBlueprintResource extends Resource
{
    protected static ?string $model = AnimalBlueprint::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static UnitEnum|string|null $navigationGroup = 'Blueprint Engine';

    protected static ?string $navigationLabel = 'Animal Blueprints';

    protected static ?string $modelLabel = 'Animal Blueprint';

    protected static ?string $pluralModelLabel = 'Animal Blueprints';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'species.name'];
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
        return $record->status === 'draft' && (auth()->user()?->hasPlatformPermission('configuration.manage') ?? false);
    }

    public static function form(Schema $schema): Schema
    {
        return AnimalBlueprintForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnimalBlueprintsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnimalBlueprints::route('/'),
            'create' => CreateAnimalBlueprint::route('/create'),
            'view' => Pages\ViewAnimalBlueprint::route('/{record}'),
            'edit' => EditAnimalBlueprint::route('/{record}/edit'),
            'preview' => PreviewAnimalBlueprint::route('/{record}/preview'),
        ];
    }
}

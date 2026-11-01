<?php

namespace App\Filament\Resources\AnimalSpecies;

use App\Filament\Resources\AnimalSpecies\Pages\CreateAnimalSpecies;
use App\Filament\Resources\AnimalSpecies\Pages\EditAnimalSpecies;
use App\Filament\Resources\AnimalSpecies\Pages\ListAnimalSpecies;
use App\Filament\Resources\AnimalSpecies\Pages\ViewAnimalSpecies;
use App\Filament\Resources\AnimalSpecies\Schemas\AnimalSpeciesForm;
use App\Filament\Resources\AnimalSpecies\Tables\AnimalSpeciesTable;
use App\Models\AnimalSpecies;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AnimalSpeciesResource extends Resource
{
    protected static ?string $model = AnimalSpecies::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static UnitEnum|string|null $navigationGroup = 'Blueprint Engine';

    protected static ?string $navigationLabel = 'Dynamic Species';

    protected static ?string $modelLabel = 'Dynamic Animal Species';

    protected static ?string $pluralModelLabel = 'Dynamic Animal Species';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    /** Must match the reproduction values the backend accepts for species capabilities. */
    public const REPRODUCTION = ['live_birth' => 'Live birth', 'egg_laying' => 'Egg laying', 'spawning' => 'Spawning', 'colony' => 'Colony', 'none' => 'None'];

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
        return $record->status === 'draft' && (auth()->user()?->hasPlatformPermission('configuration.manage') ?? false);
    }

    public static function form(Schema $schema): Schema
    {
        return AnimalSpeciesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnimalSpeciesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnimalSpecies::route('/'),
            'create' => CreateAnimalSpecies::route('/create'),
            'view' => ViewAnimalSpecies::route('/{record}'),
            'edit' => EditAnimalSpecies::route('/{record}/edit'),
        ];
    }
}

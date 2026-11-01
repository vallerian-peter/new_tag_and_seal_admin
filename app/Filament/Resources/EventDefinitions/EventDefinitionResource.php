<?php

namespace App\Filament\Resources\EventDefinitions;

use App\Filament\Resources\EventDefinitions\Pages\CreateEventDefinition;
use App\Filament\Resources\EventDefinitions\Pages\EditEventDefinition;
use App\Filament\Resources\EventDefinitions\Pages\ListEventDefinitions;
use App\Filament\Resources\EventDefinitions\Schemas\EventDefinitionForm;
use App\Filament\Resources\EventDefinitions\Tables\EventDefinitionsTable;
use App\Models\EventDefinition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class EventDefinitionResource extends Resource
{
    protected static ?string $model = EventDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static UnitEnum|string|null $navigationGroup = 'Blueprint Engine';

    protected static ?string $navigationLabel = 'Event Definitions';

    protected static ?string $modelLabel = 'Event Definition';

    protected static ?string $pluralModelLabel = 'Event Definitions';

    protected static ?int $navigationSort = 2;

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
        return $record->status === 'draft' && (auth()->user()?->hasPlatformPermission('configuration.manage') ?? false);
    }

    public static function form(Schema $schema): Schema
    {
        return EventDefinitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventDefinitionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventDefinitions::route('/'),
            'create' => CreateEventDefinition::route('/create'),
            'view' => Pages\ViewEventDefinition::route('/{record}'),
            'edit' => EditEventDefinition::route('/{record}/edit'),
        ];
    }
}

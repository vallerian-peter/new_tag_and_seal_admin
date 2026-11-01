<?php

namespace App\Filament\Resources\FormDefinitions;

use App\Filament\Resources\FormDefinitions\Pages\CreateFormDefinition;
use App\Filament\Resources\FormDefinitions\Pages\EditFormDefinition;
use App\Filament\Resources\FormDefinitions\Pages\ListFormDefinitions;
use App\Filament\Resources\FormDefinitions\Pages\PreviewFormDefinition;
use App\Filament\Resources\FormDefinitions\Schemas\FormDefinitionForm;
use App\Filament\Resources\FormDefinitions\Tables\FormDefinitionsTable;
use App\Models\FormDefinition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class FormDefinitionResource extends Resource
{
    protected static ?string $model = FormDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Blueprint Engine';

    protected static ?string $navigationLabel = 'Form Definitions';

    protected static ?string $modelLabel = 'Form Definition';

    protected static ?string $pluralModelLabel = 'Form Definitions';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code'];
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
        return FormDefinitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormDefinitionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormDefinitions::route('/'),
            'create' => CreateFormDefinition::route('/create'),
            'view' => Pages\ViewFormDefinition::route('/{record}'),
            'edit' => EditFormDefinition::route('/{record}/edit'),
            'preview' => PreviewFormDefinition::route('/{record}/preview'),
        ];
    }
}

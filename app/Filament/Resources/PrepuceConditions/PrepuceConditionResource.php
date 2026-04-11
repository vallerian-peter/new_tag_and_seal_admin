<?php

namespace App\Filament\Resources\PrepuceConditions;

use App\Filament\Resources\PrepuceConditions\Pages\CreatePrepuceCondition;
use App\Filament\Resources\PrepuceConditions\Pages\EditPrepuceCondition;
use App\Filament\Resources\PrepuceConditions\Pages\ListPrepuceConditions;
use App\Filament\Resources\PrepuceConditions\Schemas\PrepuceConditionForm;
use App\Filament\Resources\PrepuceConditions\Tables\PrepuceConditionsTable;
use App\Models\PrepuceCondition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PrepuceConditionResource extends Resource
{
    protected static ?string $model = PrepuceCondition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Prepuce Conditions';

    protected static ?string $modelLabel = 'Prepuce Condition';

    protected static ?string $pluralModelLabel = 'Prepuce Conditions';

    protected static ?int $navigationSort = 18;

    protected static ?string $recordTitleAttribute = 'uuid';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        $record->loadMissing(['livestock', 'farm']);

        $livestockTag = $record->livestock?->identificationNumber ?? 'N/A';
        $farmName = $record->farm?->name ?? 'N/A';

        return "Prepuce Condition - {$livestockTag} ({$farmName})";
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'uuid',
            'conditionType.name',
            'severity.name',
            'causeRisk.name',
            'breedingStatus.name',
            'healingStatus.name',
            'livestock.identificationNumber',
            'farm.name',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with([
            'livestock',
            'farm',
            'conditionType',
            'severity',
            'causeRisk',
            'breedingStatus',
            'healingStatus',
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'conditionType',
            'severity',
            'breedingStatus',
            'healingStatus',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return PrepuceConditionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrepuceConditionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrepuceConditions::route('/'),
            'create' => CreatePrepuceCondition::route('/create'),
            'edit' => EditPrepuceCondition::route('/{record}/edit'),
        ];
    }
}


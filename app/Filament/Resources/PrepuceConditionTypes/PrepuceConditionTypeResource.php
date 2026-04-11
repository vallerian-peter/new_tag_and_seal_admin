<?php

namespace App\Filament\Resources\PrepuceConditionTypes;

use App\Filament\Resources\PrepuceConditionTypes\Pages\CreatePrepuceConditionType;
use App\Filament\Resources\PrepuceConditionTypes\Pages\EditPrepuceConditionType;
use App\Filament\Resources\PrepuceConditionTypes\Pages\ListPrepuceConditionTypes;
use App\Filament\Resources\PrepuceConditionTypes\Schemas\PrepuceConditionTypeForm;
use App\Filament\Resources\PrepuceConditionTypes\Tables\PrepuceConditionTypesTable;
use App\Models\PrepuceConditionType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PrepuceConditionTypeResource extends Resource
{
    protected static ?string $model = PrepuceConditionType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Prepuce Condition Types';

    protected static ?string $modelLabel = 'Prepuce Condition Type';

    protected static ?string $pluralModelLabel = 'Prepuce Condition Types';

    protected static ?int $navigationSort = 31;

    public static function form(Schema $schema): Schema
    {
        return PrepuceConditionTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrepuceConditionTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrepuceConditionTypes::route('/'),
            'create' => CreatePrepuceConditionType::route('/create'),
            'edit' => EditPrepuceConditionType::route('/{record}/edit'),
        ];
    }
}

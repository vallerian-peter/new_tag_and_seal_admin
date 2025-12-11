<?php

namespace App\Filament\Resources\WeightChanges;

use App\Filament\Resources\WeightChanges\Pages\CreateWeightChange;
use App\Filament\Resources\WeightChanges\Pages\EditWeightChange;
use App\Filament\Resources\WeightChanges\Pages\ListWeightChanges;
use App\Filament\Resources\WeightChanges\Schemas\WeightChangeForm;
use App\Filament\Resources\WeightChanges\Tables\WeightChangesTable;
use App\Models\WeightChange;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WeightChangeResource extends Resource
{
    protected static ?string $model = WeightChange::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Weight Changes';

    protected static ?string $modelLabel = 'Weight Change';

    protected static ?string $pluralModelLabel = 'Weight Changes';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return WeightChangeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WeightChangesTable::configure($table);
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
            'index' => ListWeightChanges::route('/'),
            'create' => CreateWeightChange::route('/create'),
            'edit' => EditWeightChange::route('/{record}/edit'),
        ];
    }
}



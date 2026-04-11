<?php

namespace App\Filament\Resources\StageChanges;

use App\Filament\Resources\StageChanges\Pages\CreateStageChange;
use App\Filament\Resources\StageChanges\Pages\EditStageChange;
use App\Filament\Resources\StageChanges\Pages\ListStageChanges;
use App\Filament\Resources\StageChanges\Schemas\StageChangeForm;
use App\Filament\Resources\StageChanges\Tables\StageChangesTable;
use App\Models\StageChange;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StageChangeResource extends Resource
{
    protected static ?string $model = StageChange::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';
    protected static ?string $navigationLabel = 'Stage Changes';
    protected static ?string $modelLabel = 'Stage Change';
    protected static ?string $pluralModelLabel = 'Stage Changes';
    protected static ?int $navigationSort = 23;

    public static function form(Schema $schema): Schema
    {
        return StageChangeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StageChangesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStageChanges::route('/'),
            'create' => CreateStageChange::route('/create'),
            'edit' => EditStageChange::route('/{record}/edit'),
        ];
    }
}


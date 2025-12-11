<?php

namespace App\Filament\Resources\Stages;

use App\Filament\Resources\Stages\Pages\CreateStage;
use App\Filament\Resources\Stages\Pages\EditStage;
use App\Filament\Resources\Stages\Pages\ListStages;
use App\Filament\Resources\Stages\Schemas\StageForm;
use App\Filament\Resources\Stages\Tables\StagesTable;
use App\Models\Stage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Stages';

    protected static ?string $modelLabel = 'Stage';

    protected static ?string $pluralModelLabel = 'Stages';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return StageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStages::route('/'),
            'create' => CreateStage::route('/create'),
            'edit' => EditStage::route('/{record}/edit'),
        ];
    }
}


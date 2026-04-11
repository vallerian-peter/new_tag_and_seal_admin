<?php

namespace App\Filament\Resources\PrepuceSeverities;

use App\Filament\Resources\PrepuceSeverities\Pages\CreatePrepuceSeverity;
use App\Filament\Resources\PrepuceSeverities\Pages\EditPrepuceSeverity;
use App\Filament\Resources\PrepuceSeverities\Pages\ListPrepuceSeverities;
use App\Filament\Resources\PrepuceSeverities\Schemas\PrepuceSeverityForm;
use App\Filament\Resources\PrepuceSeverities\Tables\PrepuceSeveritiesTable;
use App\Models\PrepuceSeverity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PrepuceSeverityResource extends Resource
{
    protected static ?string $model = PrepuceSeverity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Prepuce Severities';

    protected static ?string $modelLabel = 'Prepuce Severity';

    protected static ?string $pluralModelLabel = 'Prepuce Severities';

    protected static ?int $navigationSort = 32;

    public static function form(Schema $schema): Schema
    {
        return PrepuceSeverityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrepuceSeveritiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrepuceSeverities::route('/'),
            'create' => CreatePrepuceSeverity::route('/create'),
            'edit' => EditPrepuceSeverity::route('/{record}/edit'),
        ];
    }
}

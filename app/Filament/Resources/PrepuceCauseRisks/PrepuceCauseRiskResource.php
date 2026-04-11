<?php

namespace App\Filament\Resources\PrepuceCauseRisks;

use App\Filament\Resources\PrepuceCauseRisks\Pages\CreatePrepuceCauseRisk;
use App\Filament\Resources\PrepuceCauseRisks\Pages\EditPrepuceCauseRisk;
use App\Filament\Resources\PrepuceCauseRisks\Pages\ListPrepuceCauseRisks;
use App\Filament\Resources\PrepuceCauseRisks\Schemas\PrepuceCauseRiskForm;
use App\Filament\Resources\PrepuceCauseRisks\Tables\PrepuceCauseRisksTable;
use App\Models\PrepuceCauseRisk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PrepuceCauseRiskResource extends Resource
{
    protected static ?string $model = PrepuceCauseRisk::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Prepuce Cause / Risks';

    protected static ?string $modelLabel = 'Prepuce Cause / Risk';

    protected static ?string $pluralModelLabel = 'Prepuce Cause / Risks';

    protected static ?int $navigationSort = 34;

    public static function form(Schema $schema): Schema
    {
        return PrepuceCauseRiskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrepuceCauseRisksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrepuceCauseRisks::route('/'),
            'create' => CreatePrepuceCauseRisk::route('/create'),
            'edit' => EditPrepuceCauseRisk::route('/{record}/edit'),
        ];
    }
}

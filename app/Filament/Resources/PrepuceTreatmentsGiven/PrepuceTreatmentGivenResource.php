<?php

namespace App\Filament\Resources\PrepuceTreatmentsGiven;

use App\Filament\Resources\PrepuceTreatmentsGiven\Pages\CreatePrepuceTreatmentGiven;
use App\Filament\Resources\PrepuceTreatmentsGiven\Pages\EditPrepuceTreatmentGiven;
use App\Filament\Resources\PrepuceTreatmentsGiven\Pages\ListPrepuceTreatmentsGiven;
use App\Filament\Resources\PrepuceTreatmentsGiven\Schemas\PrepuceTreatmentGivenForm;
use App\Filament\Resources\PrepuceTreatmentsGiven\Tables\PrepuceTreatmentsGivenTable;
use App\Models\PrepuceTreatmentGiven;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PrepuceTreatmentGivenResource extends Resource
{
    protected static ?string $model = PrepuceTreatmentGiven::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Prepuce Treatments Given';

    protected static ?string $modelLabel = 'Prepuce Treatment Given';

    protected static ?string $pluralModelLabel = 'Prepuce Treatments Given';

    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return PrepuceTreatmentGivenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrepuceTreatmentsGivenTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrepuceTreatmentsGiven::route('/'),
            'create' => CreatePrepuceTreatmentGiven::route('/create'),
            'edit' => EditPrepuceTreatmentGiven::route('/{record}/edit'),
        ];
    }
}

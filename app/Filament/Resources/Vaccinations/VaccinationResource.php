<?php

namespace App\Filament\Resources\Vaccinations;

use App\Filament\Resources\Vaccinations\Pages\CreateVaccination;
use App\Filament\Resources\Vaccinations\Pages\EditVaccination;
use App\Filament\Resources\Vaccinations\Pages\ListVaccinations;
use App\Filament\Resources\Vaccinations\Schemas\VaccinationForm;
use App\Filament\Resources\Vaccinations\Tables\VaccinationsTable;
use App\Models\Vaccination;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class VaccinationResource extends Resource
{
    protected static ?string $model = Vaccination::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Vaccinations';

    protected static ?string $modelLabel = 'Vaccination';

    protected static ?string $pluralModelLabel = 'Vaccinations';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return VaccinationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VaccinationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVaccinations::route('/'),
            'create' => CreateVaccination::route('/create'),
            'edit' => EditVaccination::route('/{record}/edit'),
        ];
    }
}


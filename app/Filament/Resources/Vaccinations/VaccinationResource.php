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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class VaccinationResource extends Resource
{
    protected static ?string $model = Vaccination::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Vaccinations';

    protected static ?string $modelLabel = 'Vaccination';

    protected static ?string $pluralModelLabel = 'Vaccinations';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'uuid';

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        $record->loadMissing(['livestock', 'farm', 'vaccine']);
        $livestockTag = $record->livestock?->identificationNumber ?? 'N/A';
        $farmName = $record->farm?->name ?? 'N/A';
        $vaccine = $record->vaccine?->name ?? 'N/A';
        return "Vaccination - {$livestockTag} ({$farmName}) - {$vaccine}";
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['uuid', 'vaccinationNo', 'livestock.identificationNumber', 'farm.name', 'vaccine.name'];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['livestock', 'farm', 'vaccine']);
    }

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


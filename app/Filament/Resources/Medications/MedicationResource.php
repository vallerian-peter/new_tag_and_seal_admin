<?php

namespace App\Filament\Resources\Medications;

use App\Filament\Resources\Medications\Pages\CreateMedication;
use App\Filament\Resources\Medications\Pages\EditMedication;
use App\Filament\Resources\Medications\Pages\ListMedications;
use App\Filament\Resources\Medications\Schemas\MedicationForm;
use App\Filament\Resources\Medications\Tables\MedicationsTable;
use App\Models\Treatment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class MedicationResource extends Resource
{
    protected static ?string $model = Treatment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Treatments';

    protected static ?string $modelLabel = 'Treatment';

    protected static ?string $pluralModelLabel = 'Treatments';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'uuid';

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        $record->loadMissing(['livestock', 'farm', 'disease', 'medicine']);
        $livestockTag = $record->livestock?->identificationNumber ?? 'N/A';
        $farmName = $record->farm?->name ?? 'N/A';
        $disease = $record->disease?->name ?? 'N/A';
        return "Treatment - {$livestockTag} ({$farmName}) - {$disease}";
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['uuid', 'remarks', 'livestock.identificationNumber', 'farm.name', 'disease.name', 'medicine.name'];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['livestock', 'farm', 'disease', 'medicine']);
    }

    public static function form(Schema $schema): Schema
    {
        return MedicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedications::route('/'),
            'create' => CreateMedication::route('/create'),
            'edit' => EditMedication::route('/{record}/edit'),
        ];
    }
}

